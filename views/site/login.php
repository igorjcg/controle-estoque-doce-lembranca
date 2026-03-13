<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>

<div class="d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="card shadow-sm border-0" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <h1 class="h4 mb-4 text-center">
                <?= Html::img('@web/img/logoAntiga.jpg', [
                    'class' => 'img-fluid',
                    'style' => 'max-height:120px; width:auto;'
                ]) ?>
            </h1>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableClientValidation' => true,
            ]); ?>

            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder' => 'Usuario',
            ]) ?>

            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => 'Senha',
            ]) ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                 'label' => 'Lembre-me']) ?>

            <div class="d-grid mt-3">
                <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary']) ?>
            </div>

            <div class="text-center mt-3">
                <?= Html::a('Criar usuário', ['/usuario/create']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
