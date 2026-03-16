<?php

namespace app\controllers;

use Yii;
use app\models\Ingrediente;
use app\models\MovimentacaoEstoque;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class MovimentacaoEstoqueController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Registra entrada de estoque.
     */
    public function actionEntrada()
    {
        return $this->processarMovimentacao('entrada');
    }

    /**
     * Registra saída de estoque.
     */
    public function actionSaida()
    {
        return $this->processarMovimentacao('saida');
    }

    /**
     * Exibe histórico de movimentações.
     */
    public function actionHistorico()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MovimentacaoEstoque::find()
                ->with(['ingrediente.unidadeMedida', 'criadoPor'])
                ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 30],
        ]);

        return $this->render('historico', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Processa entrada/saída: apenas registra movimentação.
     * Na entrada, atualiza custo médio do ingrediente.
     * Quantidade deve estar sempre na unidade base.
     */
    protected function processarMovimentacao(string $tipoMovimento)
    {
        $model = new MovimentacaoEstoque();
        $model->tipo_movimento = $tipoMovimento;

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $ingrediente = Ingrediente::find()
                    ->andWhere(['id' => (int) $model->ingrediente_id])
                    ->one();
                if ($ingrediente === null) {
                    throw new NotFoundHttpException('Ingrediente não encontrado.');
                }

                $quantidade = (string) $model->quantidade;
                if (bccomp($quantidade, '0', 6) <= 0) {
                    $model->addError('quantidade', 'A quantidade deve ser maior que zero.');
                    throw new Exception('Quantidade inválida.');
                }

                $estoqueAtual = $ingrediente->getEstoqueAtualDecimal();
                if ($tipoMovimento === 'saida') {
                    if (bccomp($estoqueAtual, $quantidade, 6) < 0) {
                        $model->addError('quantidade', 'Estoque insuficiente para a saída informada.');
                        throw new Exception('Estoque insuficiente.');
                    }
                }

                // Garante valor_unitario e valor_total: se um faltar, calcula a partir do outro (precisão decimal).
                $scale = 6;
                if (($model->valor_unitario !== null && $model->valor_unitario !== '') && ($model->valor_total === null || $model->valor_total === '')) {
                    $model->valor_total = bcmul((string) $model->valor_unitario, $quantidade, $scale);
                } elseif (($model->valor_total !== null && $model->valor_total !== '') && ($model->valor_unitario === null || $model->valor_unitario === '') && bccomp($quantidade, '0', $scale) > 0) {
                    $model->valor_unitario = bcdiv((string) $model->valor_total, $quantidade, $scale);
                }

                if (!$model->save()) {
                    throw new Exception('Falha ao salvar movimentação.');
                }

                // Custo médio: apenas em ENTRADA, pela média ponderada (Weighted Average Cost).
                // novo_custo_medio = ((quantidade_atual * custo_medio_atual) + (quantidade_entrada * custo_unitario_compra)) / (quantidade_atual + quantidade_entrada)
                if ($tipoMovimento === 'entrada' && $model->valor_unitario !== null && $model->valor_unitario !== '' && bccomp((string) $model->valor_unitario, '0', $scale) > 0) {
                    $custoAtual = (string) $ingrediente->custo_medio;
                    $valorUnitarioEntrada = (string) $model->valor_unitario;
                    $somaQty = bcadd($estoqueAtual, $quantidade, $scale);
                    if (bccomp($somaQty, '0', $scale) > 0) {
                        $numerador = bcadd(
                            bcmul($estoqueAtual, $custoAtual, 10),
                            bcmul($quantidade, $valorUnitarioEntrada, 10),
                            10
                        );
                        $novoCustoMedio = bcdiv($numerador, $somaQty, 6);
                        $ingrediente->custo_medio = round((float) $novoCustoMedio, 4);
                        $ingrediente->save(false);
                    } else {
                        $ingrediente->custo_medio = round((float) $valorUnitarioEntrada, 4);
                        $ingrediente->save(false);
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Movimentação registrada com sucesso.');
                return $this->redirect(['historico']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                if (!$model->hasErrors()) {
                    Yii::$app->session->setFlash('error', 'Não foi possível registrar a movimentação.');
                }
            }
        }

        return $this->render($tipoMovimento, [
            'model' => $model,
        ]);
    }
}