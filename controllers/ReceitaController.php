<?php

namespace app\controllers;

use Yii;
use app\models\Receita;
use app\models\ReceitaIngrediente;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ReceitaController extends Controller
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
     * Lista receitas ativas.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Receita::find()->orderBy(['nome' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Exibe detalhes de uma receita e seus ingredientes.
     */
    public function actionView($id)
    {
        $model = $this->findModel((int)$id);
        $ingredientesReceita = ReceitaIngrediente::find()
            ->where(['receita_id' => $model->id])
            ->with(['ingrediente', 'unidadeMedida'])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'ingredientesReceita' => $ingredientesReceita,
        ]);
    }

    /**
     * Cria uma receita e permite cadastrar os ingredientes associados.
     */
    public function actionCreate()
    {
        $model = new Receita();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new Exception('Falha ao salvar receita.');
                }

                $itensReceita = $this->extrairItensReceita(Yii::$app->request->post('ReceitaIngrediente', []));
                if (!$this->salvarIngredientesReceita($model->id, $itensReceita)) {
                    throw new Exception('Falha ao salvar ingredientes da receita.');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Receita cadastrada com sucesso.');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Não foi possível cadastrar a receita.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Atualiza uma receita e seus ingredientes associados.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel((int)$id);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new Exception('Falha ao atualizar receita.');
                }

                $itensReceita = $this->extrairItensReceita(Yii::$app->request->post('ReceitaIngrediente', []));
                if (!$this->salvarIngredientesReceita($model->id, $itensReceita)) {
                    throw new Exception('Falha ao atualizar ingredientes da receita.');
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Receita atualizada com sucesso.');
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Não foi possível atualizar a receita.');
            }
        }

        $ingredientesReceita = ReceitaIngrediente::find()
            ->where(['receita_id' => $model->id])
            ->with(['ingrediente', 'unidadeMedida'])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'ingredientesReceita' => $ingredientesReceita,
        ]);
    }

    /**
     * Exclui logicamente uma receita (soft delete).
     */
    public function actionDelete($id)
    {
        $model = $this->findModel((int)$id);
        $model->flag_del = 1;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Receita removida com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover a receita.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Salva os itens da receita na tabela pivô.
     */
    protected function salvarIngredientesReceita(int $receitaId, array $itensReceita): bool
    {
        ReceitaIngrediente::deleteAll(['receita_id' => $receitaId]);

        foreach ($itensReceita as $item) {
            if (empty($item['ingrediente_id']) || empty($item['unidade_medida_id']) || !isset($item['quantidade'])) {
                continue;
            }

            $receitaIngrediente = new ReceitaIngrediente();
            $receitaIngrediente->receita_id = $receitaId;
            $receitaIngrediente->ingrediente_id = (int)$item['ingrediente_id'];
            $receitaIngrediente->unidade_medida_id = (int)$item['unidade_medida_id'];
            $receitaIngrediente->quantidade = (float)$item['quantidade'];

            if (!$receitaIngrediente->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Normaliza o payload de ingredientes da receita.
     */
    protected function extrairItensReceita(array $payload): array
    {
        if ($payload === []) {
            return [];
        }

        $primeiro = reset($payload);
        if (is_array($primeiro) && array_key_exists('ingrediente_id', $primeiro)) {
            return $payload;
        }

        $ingredientes = $payload['ingrediente_id'] ?? [];
        $unidades = $payload['unidade_medida_id'] ?? [];
        $quantidades = $payload['quantidade'] ?? [];
        $total = max(count($ingredientes), count($unidades), count($quantidades));

        $itens = [];
        for ($i = 0; $i < $total; $i++) {
            $itens[] = [
                'ingrediente_id' => $ingredientes[$i] ?? null,
                'unidade_medida_id' => $unidades[$i] ?? null,
                'quantidade' => $quantidades[$i] ?? null,
            ];
        }

        return $itens;
    }

    /**
     * Busca a receita ativa pelo ID.
     */
    protected function findModel(int $id): Receita
    {
        $model = Receita::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Receita não encontrada.');
        }

        return $model;
    }
}
