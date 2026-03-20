<?php

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var string $token */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>

<div class="user-form">
    <?php $form = ActiveForm::begin([
        'action' => ['create', 'token' => $token],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->input('email', ['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'confirmar_password')->passwordInput(['maxlength' => true]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
