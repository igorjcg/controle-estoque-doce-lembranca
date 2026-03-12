<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UnidadeMedida $model */
?>

<div class="unidade-medida-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'sigla')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'categoria')->dropDownList([
                'peso' => 'peso',
                'volume' => 'volume',
                'unidade' => 'unidade',
            ], ['prompt' => 'Selecione']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fator_base')->textInput(['type' => 'number', 'step' => '0.0001']) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
