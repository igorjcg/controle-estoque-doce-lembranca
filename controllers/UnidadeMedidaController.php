<?php

namespace app\controllers;

use Yii;
use app\models\UnidadeMedida;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UnidadeMedidaController extends Controller
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
     * Lista unidades de medida ativas.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UnidadeMedida::find()->orderBy(['nome' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Cria uma nova unidade de medida.
     */
    public function actionCreate()
    {
        $model = new UnidadeMedida();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Unidade de medida cadastrada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Atualiza uma unidade de medida existente.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel((int)$id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Unidade de medida atualizada com sucesso.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Exclui logicamente uma unidade de medida (soft delete).
     */
    public function actionDelete($id)
    {
        $model = $this->findModel((int)$id);
        $model->flag_del = 1;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Unidade de medida removida com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover a unidade de medida.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Busca a unidade de medida ativa pelo ID.
     */
    protected function findModel(int $id): UnidadeMedida
    {
        $model = UnidadeMedida::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Unidade de medida não encontrada.');
        }

        return $model;
    }
}
