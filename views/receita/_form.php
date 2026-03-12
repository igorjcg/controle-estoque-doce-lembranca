<?php

use app\models\Ingrediente;
use app\models\UnidadeMedida;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Receita $model */
/** @var app\models\ReceitaIngrediente[] $ingredientesReceita */

$ingredientesReceita = $ingredientesReceita ?? [];
$ingredientes = ArrayHelper::map(Ingrediente::find()->orderBy(['nome' => SORT_ASC])->all(), 'id', 'nome');
$unidades = ArrayHelper::map(
    UnidadeMedida::find()->orderBy(['nome' => SORT_ASC])->all(),
    'id',
    static fn($item) => $item->nome . ' (' . $item->sigla . ')'
);
$ingredienteOptions = Html::renderSelectOptions(null, $ingredientes);
$unidadeOptions = Html::renderSelectOptions(null, $unidades);
?>

<div class="receita-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'descricao')->textarea(['rows' => 3]) ?>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Ingredientes da Receita</strong>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-ingrediente">Adicionar Linha</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tabela-ingredientes">
                    <thead>
                    <tr>
                        <th>Ingrediente</th>
                        <th>Unidade</th>
                        <th>Quantidade</th>
                        <th style="width: 80px;">Acao</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ingredientesReceita)): ?>
                        <tr>
                            <td><?= Html::dropDownList('ReceitaIngrediente[ingrediente_id][]', null, $ingredientes, ['class' => 'form-select', 'prompt' => 'Selecione']) ?></td>
                            <td><?= Html::dropDownList('ReceitaIngrediente[unidade_medida_id][]', null, $unidades, ['class' => 'form-select', 'prompt' => 'Selecione']) ?></td>
                            <td><?= Html::input('number', 'ReceitaIngrediente[quantidade][]', null, ['class' => 'form-control', 'step' => '0.001']) ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover">Remover</button></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ingredientesReceita as $item): ?>
                            <tr>
                                <td><?= Html::dropDownList('ReceitaIngrediente[ingrediente_id][]', $item->ingrediente_id, $ingredientes, ['class' => 'form-select', 'prompt' => 'Selecione']) ?></td>
                                <td><?= Html::dropDownList('ReceitaIngrediente[unidade_medida_id][]', $item->unidade_medida_id, $unidades, ['class' => 'form-select', 'prompt' => 'Selecione']) ?></td>
                                <td><?= Html::input('number', 'ReceitaIngrediente[quantidade][]', $item->quantidade, ['class' => 'form-control', 'step' => '0.001']) ?></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover">Remover</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<template id="receita-ingrediente-row-template">
    <tr>
        <td><select name="ReceitaIngrediente[ingrediente_id][]" class="form-select"><option value="">Selecione</option><?= $ingredienteOptions ?></select></td>
        <td><select name="ReceitaIngrediente[unidade_medida_id][]" class="form-select"><option value="">Selecione</option><?= $unidadeOptions ?></select></td>
        <td><input type="number" name="ReceitaIngrediente[quantidade][]" class="form-control" step="0.001"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover">Remover</button></td>
    </tr>
</template>

<?php
$this->registerJsFile('@web/views/receita/js/_form.js.php', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
