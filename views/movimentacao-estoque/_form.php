<?php

use app\models\Ingrediente;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MovimentacaoEstoque $model */

$ingredientes = ArrayHelper::map(
    Ingrediente::find()->orderBy(['nome' => SORT_ASC])->all(),
    'id',
    static fn($item) => $item->nome . ' (' . ($item->unidadeMedida->sigla ?? '-') . ')'
);
?>

<div class="movimentacao-estoque-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'ingrediente_id')->dropDownList($ingredientes, ['prompt' => 'Selecione']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'quantidade')->textInput(['type' => 'number', 'step' => '0.001'])
                ->hint('Sempre na unidade base do ingrediente.') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'valor_unitario')->textInput(['type' => 'number', 'step' => '0.0001']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'valor_total')->textInput(['type' => 'number', 'step' => '0.0001']) ?>
        </div>
    </div>

    <?= $form->field($model, 'observacao')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Historico', ['historico'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
