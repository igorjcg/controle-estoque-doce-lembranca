<?php

namespace app\controllers;

use app\models\Ingrediente;
use app\models\MovimentacaoEstoque;
use app\models\Producao;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $ingredientes = Ingrediente::find()->with('unidadeMedida')->orderBy(['nome' => SORT_ASC])->all();
        $totalIngredientes = count($ingredientes);
        $ingredientesEstoqueBaixo = array_values(array_filter($ingredientes, function (Ingrediente $i) {
            $estoqueAtual = $i->getEstoqueAtual();
            $minimo = (float) $i->estoque_minimo_alerta;
            return $minimo > 0 && $estoqueAtual <= $minimo;
        }));
        $totalEstoqueBaixo = count($ingredientesEstoqueBaixo);

        // Centraliza os indicadores do dashboard em arrays simples para a view.
        $estoquePorIngrediente = [];
        $valorTotalEstoque = 0.0;

        foreach ($ingredientes as $ingrediente) {
            $estoqueAtual = $ingrediente->getEstoqueAtual();
            $estoquePorIngrediente[] = [
                'nome' => $ingrediente->nome,
                'quantidade' => $estoqueAtual,
                'quantidade_formatada' => $ingrediente->estoqueAtualComUnidadeFormatado,
                'valor_estoque' => max($estoqueAtual, 0) * (float) $ingrediente->custo_medio,
            ];
            $valorTotalEstoque += max($estoqueAtual, 0) * (float) $ingrediente->custo_medio;
        }

        usort($estoquePorIngrediente, static function (array $a, array $b) {
            return $a['quantidade'] <=> $b['quantidade'];
        });

        $menorEstoque = array_slice($estoquePorIngrediente, 0, 10);
        $graficoMenorEstoque = [
            'labels' => array_column($menorEstoque, 'nome'),
            'quantidades' => array_map(static fn(array $item) => round((float) $item['quantidade'], 3), $menorEstoque),
            'quantidadesFormatadas' => array_column($menorEstoque, 'quantidade_formatada'),
        ];

        usort($estoquePorIngrediente, static function (array $a, array $b) {
            return $b['valor_estoque'] <=> $a['valor_estoque'];
        });

        $distribuicaoBase = array_filter($estoquePorIngrediente, static function (array $item) {
            return $item['valor_estoque'] > 0;
        });
        $distribuicaoTop = array_slice($distribuicaoBase, 0, 8);
        $valorOutros = array_sum(array_column(array_slice($distribuicaoBase, 8), 'valor_estoque'));

        $graficoDistribuicao = [
            'labels' => array_column($distribuicaoTop, 'nome'),
            'valores' => array_map(static fn(array $item) => round((float) $item['valor_estoque'], 2), $distribuicaoTop),
        ];

        if ($valorOutros > 0) {
            $graficoDistribuicao['labels'][] = 'Outros';
            $graficoDistribuicao['valores'][] = round($valorOutros, 2);
        }

        $inicioHoje = strtotime(date('Y-m-d 00:00:00'));
        $fimHoje = strtotime(date('Y-m-d 23:59:59'));
        $totalMovimentacoesHoje = (int) MovimentacaoEstoque::find()
            ->andWhere(['between', 'created_at', $inicioHoje, $fimHoje])
            ->count();
        $totalProducoesRegistradas = (int) Producao::find()->count();

        // Gera a série diária dos últimos 7 dias e preenche os dias sem movimentação com zero.
        $dias = [];
        $inicioPeriodo = strtotime('-6 days', $inicioHoje);
        for ($timestamp = $inicioPeriodo; $timestamp <= $inicioHoje; $timestamp += 86400) {
            $dias[date('Y-m-d', $timestamp)] = [
                'label' => Yii::$app->formatter->asDate($timestamp, 'php:d/m'),
                'entrada' => 0.0,
                'saida' => 0.0,
            ];
        }

        $movimentacoesPeriodo = MovimentacaoEstoque::find()
            ->select(['created_at', 'tipo_movimento', 'quantidade'])
            ->where(['between', 'created_at', $inicioPeriodo, $fimHoje])
            ->asArray()
            ->all();

        foreach ($movimentacoesPeriodo as $movimentacao) {
            $dia = date('Y-m-d', (int) $movimentacao['created_at']);
            if (!isset($dias[$dia])) {
                continue;
            }

            $tipo = $movimentacao['tipo_movimento'];
            $dias[$dia][$tipo] += (float) $movimentacao['quantidade'];
        }

        $graficoMovimentacoes = [
            'labels' => array_column($dias, 'label'),
            'entradas' => array_map(static fn(array $dia) => $dia['entrada'], $dias),
            'saidas' => array_map(static fn(array $dia) => $dia['saida'], $dias),
        ];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $ingredientes,
            'pagination' => ['pageSize' => 15],
            'sort' => [
                'attributes' => ['nome'],
                'defaultOrder' => ['nome' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
            'totalIngredientes' => $totalIngredientes,
            'totalEstoqueBaixo' => $totalEstoqueBaixo,
            'valorTotalEstoque' => $valorTotalEstoque,
            'totalMovimentacoesHoje' => $totalMovimentacoesHoje,
            'totalProducoesRegistradas' => $totalProducoesRegistradas,
            'ingredientesEstoqueBaixo' => $ingredientesEstoqueBaixo,
            'graficoMenorEstoque' => $graficoMenorEstoque,
            'graficoMovimentacoes' => $graficoMovimentacoes,
            'graficoDistribuicao' => $graficoDistribuicao,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
