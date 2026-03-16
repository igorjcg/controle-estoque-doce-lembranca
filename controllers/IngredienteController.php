<?php

namespace app\controllers;

use Yii;
use app\models\Ingrediente;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class IngredienteController extends BaseController
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
     * Lista ingredientes ativos.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Ingrediente::find()->with('unidadeMedida')->orderBy(['nome' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Exibe detalhes de um ingrediente.
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel((int)$id),
        ]);
    }

    /**
     * Cria um novo ingrediente.
     */
    public function actionCreate()
    {
        $model = new Ingrediente();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ingrediente cadastrado com sucesso.');
            return $this->redirectAfterCreate();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Atualiza um ingrediente existente.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel((int)$id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ingrediente atualizado com sucesso.');
            return $this->redirectAfterCreate();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Exclui logicamente um ingrediente (soft delete).
     */
    public function actionDelete($id)
    {
        $model = $this->findModel((int)$id);
        $model->flag_del = 1;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Ingrediente removido com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover o ingrediente.');
        }

        return $this->redirectAfterCreate();
    }

    /**
     * Lista ingredientes com estoque em nível de alerta (getEstoqueAtual() <= estoque_minimo_alerta).
     */
    public function actionEstoqueBaixo()
    {
        $todos = Ingrediente::find()->with('unidadeMedida')->orderBy(['nome' => SORT_ASC])->all();
        $comEstoqueBaixo = array_filter($todos, function (Ingrediente $i) {
            $minimo = (float) $i->estoque_minimo_alerta;
            return $minimo > 0 && $i->getEstoqueAtual() <= $minimo;
        });

        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($comEstoqueBaixo),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('estoque-baixo', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Retorna a unidade base do ingrediente em JSON para uso no formulário de receitas.
     */
    public function actionUnidade($id): Response
    {
        $ingrediente = Ingrediente::find()
            ->with('unidadeMedida')
            ->andWhere(['id' => (int) $id])
            ->one();

        if ($ingrediente === null) {
            return $this->asJson([
                'unidade' => null,
                'unidade_medida_id' => null,
            ]);
        }

        return $this->asJson([
            'unidade' => $ingrediente->unidadeMedida->sigla ?? null,
            'unidade_medida_id' => $ingrediente->unidade_medida_id,
        ]);
    }

    /**
     * Busca o ingrediente ativo pelo ID.
     */
    protected function findModel(int $id): Ingrediente
    {
        $model = Ingrediente::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Ingrediente não encontrado.');
        }

        return $model;
    }
}