<?php

use app\models\Receita;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Producao $model */

$receitas = ArrayHelper::map(Receita::find()->orderBy(['nome' => SORT_ASC])->all(), 'id', 'nome');
?>

<div class="producao-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'receita_id')->dropDownList($receitas, ['prompt' => 'Selecione']) ?>
        </div>
        <div class="col-12 col-md-3">
            <?= $form->field($model, 'quantidade')->textInput(['type' => 'number', 'step' => '0.001']) ?>
        </div>
    </div>

    <?= $form->field($model, 'observacao')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3 page-actions">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary btn-responsive']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary btn-responsive']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
