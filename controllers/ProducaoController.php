<?php

namespace app\controllers;

use app\models\Ingrediente;
use app\models\Producao;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class ProducaoController extends BaseController
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Producao::find()->with(['receita', 'criadoPor'])->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Producao();

        if ($model->load(Yii::$app->request->post()) && $model->salvarComMovimentacoes()) {
            Yii::$app->session->setFlash('success', 'Produção registrada com sucesso.');
            return $this->redirectAfterCreate();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel((int) $id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel((int) $id);

        if ($model->load(Yii::$app->request->post()) && $model->salvarComMovimentacoes()) {
            Yii::$app->session->setFlash('success', 'Produção atualizada com sucesso.');
            return $this->redirectAfterCreate();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel((int) $id);

        if ($model->excluirComMovimentacoes()) {
            Yii::$app->session->setFlash('success', 'Produção removida com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível remover a produção.');
        }

        return $this->redirectAfterCreate();
    }

    public function actionAnalise()
    {
        $inicioMes = strtotime(date('Y-m-01 00:00:00'));
        $fimMes = strtotime(date('Y-m-t 23:59:59'));

        $custoTotalMes = (float) Producao::find()
            ->andWhere(['between', 'created_at', $inicioMes, $fimMes])
            ->sum('custo_total');

        $ingredientes = Ingrediente::find()->with('unidadeMedida')->orderBy(['nome' => SORT_ASC])->all();
        $valorEstoqueAtual = 0.0;
        $analiseEstoque = [];

        foreach ($ingredientes as $ingrediente) {
            $estoqueAtual = $ingrediente->getEstoqueAtual();
            $valorTotal = $estoqueAtual * (float) $ingrediente->custo_medio;
            $valorEstoqueAtual += $valorTotal;

            $analiseEstoque[] = [
                'nome' => $ingrediente->nome,
                'estoque_atual' => $ingrediente->estoqueAtualComUnidadeFormatado,
                'custo_medio' => (float) $ingrediente->custo_medio,
                'valor_total' => $valorTotal,
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $analiseEstoque,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('analise', [
            'custoTotalMes' => $custoTotalMes,
            'valorEstoqueAtual' => $valorEstoqueAtual,
            'lucroEstimadoFormula' => 'preco_venda - custo_unitario',
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel(int $id): Producao
    {
        $model = Producao::find()
            ->with(['receita', 'criadoPor', 'movimentacoesEstoque.ingrediente'])
            ->andWhere(['id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Produção não encontrada.');
        }

        return $model;
    }
}