<?php

namespace app\controllers;

use app\common\util\Util;
use app\models\ConviteUsuario;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class UsuarioController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['gerar-convite'],
                'rules' => [

                    [
                        'actions' => ['gerar-convite'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'gerar-convite' => ['post'],
                ],
            ],
        ];
    }

    public function actionGerarConvite(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $convite = new ConviteUsuario([
            'token' => Util::gerarTokenConvite(),
            'criado_por' => (int) Yii::$app->user->id,
            'criado_em' => time(),
            'expira_em' => Util::gerarExpiracaoConvite(),
        ]);

        if (!$convite->save()) {
            Yii::$app->response->statusCode = 422;

            return [
                'success' => false,
                'message' => 'Não foi possível gerar o convite.',
                'errors' => $convite->getErrors(),
            ];
        }

        return [
            'success' => true,
            'link' => Url::to(['/usuario/create', 'token' => $convite->token], true),
            'expiraEm' => $convite->expira_em,
        ];
    }

    public function actionCreate(): Response|string
    {
        $convite = $this->obterConviteValido(Yii::$app->request->get('token'));

        $model = new User();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->save()) {
                    $convite->usado = 1;
                    $convite->usado_por = (int) $model->id;

                    if (!$convite->save(false, ['usado', 'usado_por'])) {
                        throw new \RuntimeException('Não foi possível invalidar o convite.');
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Usuário criado com sucesso.');

                    return $this->redirect(['site/login']);
                }

                $transaction->rollBack();
            } catch (\Throwable $exception) {
                $transaction->rollBack();
                throw $exception;
            }
        }

        return $this->render('create', [
            'model' => $model,
            'token' => $convite->token,
        ]);
    }

    private function obterConviteValido(?string $token): ConviteUsuario
    {
        if ($token === null || $token === '') {
            throw new ForbiddenHttpException('Cadastro permitido apenas via convite.');
        }

        $convite = ConviteUsuario::findOne(['token' => $token]);

        if ($convite === null || $convite->isExpirado() || $convite->isUsado()) {
            throw new ForbiddenHttpException('Convite inválido ou expirado');
        }

        return $convite;
    }
}



