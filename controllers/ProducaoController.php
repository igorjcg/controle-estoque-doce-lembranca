<?php

namespace app\controllers;

use app\common\util\UnidadeUtil;
use app\models\MovimentacaoEstoque;
use app\models\Producao;
use app\models\Receita;
use app\models\ReceitaIngrediente;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProducaoController extends Controller
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
     * Lista producoes cadastradas.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Producao::find()->with('receita')->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Registra uma producao e as saídas de estoque (apenas movimentações).
     */
    public function actionCreate()
    {
        $model = new Producao();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $receita = Receita::find()
                    ->where(['id' => (int) $model->receita_id])
                    ->one();
                if ($receita === null) {
                    throw new NotFoundHttpException('Receita nao encontrada.');
                }

                $itensReceita = ReceitaIngrediente::find()
                    ->where(['receita_id' => $receita->id])
                    ->with(['ingrediente.unidadeMedida', 'unidadeMedida'])
                    ->all();

                if (empty($itensReceita)) {
                    throw new Exception('A receita nao possui ingredientes.');
                }

                $fatorProducao = (float) $model->quantidade;
                if ($fatorProducao <= 0) {
                    $model->addError('quantidade', 'A quantidade de producao deve ser maior que zero.');
                    throw new Exception('Quantidade de producao invalida.');
                }

                foreach ($itensReceita as $item) {
                    $ingrediente = $item->ingrediente;
                    $unidadeOrigem = $item->unidadeMedida;
                    $unidadeBaseIngrediente = $ingrediente?->unidadeMedida;

                    if ($ingrediente === null || $unidadeOrigem === null || $unidadeBaseIngrediente === null) {
                        throw new Exception('Ingrediente ou unidade de medida invalidos na receita.');
                    }

                    $quantidadeReceita = (float) $item->quantidade * $fatorProducao;
                    $quantidadeEmBase = UnidadeUtil::converterParaBase(
                        $quantidadeReceita,
                        $unidadeOrigem,
                        $unidadeBaseIngrediente
                    );

                    $estoqueAtual = $ingrediente->getEstoqueAtual();
                    if ($estoqueAtual < $quantidadeEmBase) {
                        throw new Exception("Estoque insuficiente para o ingrediente {$ingrediente->nome}.");
                    }

                    $movimentacao = new MovimentacaoEstoque();
                    $movimentacao->ingrediente_id = $ingrediente->id;
                    $movimentacao->tipo_movimento = 'saida';
                    $movimentacao->quantidade = $quantidadeEmBase;
                    $movimentacao->valor_unitario = (string) $ingrediente->custo_medio;
                    $movimentacao->valor_total = bcmul((string) $quantidadeEmBase, (string) $ingrediente->custo_medio, 6);
                    $movimentacao->observacao = "Baixa automatica da producao da receita: {$receita->nome}";

                    if (!$movimentacao->save()) {
                        throw new Exception("Falha ao registrar movimentacao do ingrediente {$ingrediente->nome}.");
                    }
                }

                if (!$model->save()) {
                    throw new Exception('Falha ao salvar producao.');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Producao registrada com sucesso.');
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                if (!$model->hasErrors()) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
