<?php

namespace app\controllers;

use app\models\Ingrediente;
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
            'ingredientesEstoqueBaixo' => $ingredientesEstoqueBaixo,
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
