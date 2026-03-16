<?php

use app\models\Ingrediente;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Receita $model */
/** @var app\models\ReceitaIngrediente[] $ingredientesReceita */

$ingredientesReceita = $ingredientesReceita ?? [];
$ingredientes = ArrayHelper::map(Ingrediente::find()->with('unidadeMedida')->orderBy(['nome' => SORT_ASC])->all(), 'id', 'nome');
$ingredienteOptions = Html::renderSelectOptions(null, $ingredientes);
$unidadeUrl = Url::to(['ingrediente/unidade']);
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
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Ingredientes da Receita</strong>
            <button type="button" class="btn btn-sm btn-outline-primary btn-responsive" id="btn-add-ingrediente">Adicionar Linha</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tabela-ingredientes" data-unidade-url="<?= Html::encode($unidadeUrl) ?>">
                    <thead>
                    <tr>
                        <th>Ingrediente</th>
                        <th>Unidade</th>
                        <th>Quantidade</th>
                        <th style="min-width: 80px;">Acao</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ingredientesReceita)): ?>
                        <tr>
                            <td>
                                <?= Html::dropDownList('ReceitaIngrediente[ingrediente_id][]', null, $ingredientes, [
                                    'class' => 'form-select js-ingrediente-select',
                                    'prompt' => 'Selecione',
                                ]) ?>
                            </td>
                            <td>
                                <?= Html::hiddenInput('ReceitaIngrediente[unidade_medida_id][]', null, ['class' => 'js-unidade-id']) ?>
                                <?= Html::textInput('ReceitaIngrediente[unidade_medida][]', null, [
                                    'class' => 'form-control js-unidade-display',
                                    'readonly' => true,
                                    'tabindex' => -1,
                                    'placeholder' => 'Selecione um ingrediente',
                                ]) ?>
                            </td>
                            <td><?= Html::input('number', 'ReceitaIngrediente[quantidade][]', null, ['class' => 'form-control', 'step' => '0.001']) ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover btn-responsive">Remover</button></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ingredientesReceita as $item): ?>
                            <tr>
                                <td>
                                    <?= Html::dropDownList('ReceitaIngrediente[ingrediente_id][]', $item->ingrediente_id, $ingredientes, [
                                        'class' => 'form-select js-ingrediente-select',
                                        'prompt' => 'Selecione',
                                    ]) ?>
                                </td>
                                <td>
                                    <?= Html::hiddenInput('ReceitaIngrediente[unidade_medida_id][]', $item->unidade_medida_id, ['class' => 'js-unidade-id']) ?>
                                    <?= Html::textInput('ReceitaIngrediente[unidade_medida][]', $item->unidadeMedida->sigla ?? '', [
                                        'class' => 'form-control js-unidade-display',
                                        'readonly' => true,
                                        'tabindex' => -1,
                                        'placeholder' => 'Selecione um ingrediente',
                                    ]) ?>
                                </td>
                                <td><?= Html::input('number', 'ReceitaIngrediente[quantidade][]', $item->quantidade, ['class' => 'form-control', 'step' => '0.001']) ?></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover btn-responsive">Remover</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group mt-3 page-actions">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary btn-responsive']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary btn-responsive']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<template id="receita-ingrediente-row-template">
    <tr>
        <td>
            <select name="ReceitaIngrediente[ingrediente_id][]" class="form-select js-ingrediente-select">
                <option value="">Selecione</option>
                <?= $ingredienteOptions ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="ReceitaIngrediente[unidade_medida_id][]" class="js-unidade-id">
            <input type="text" name="ReceitaIngrediente[unidade_medida][]" class="form-control js-unidade-display" readonly tabindex="-1" placeholder="Selecione um ingrediente">
        </td>
        <td><input type="number" name="ReceitaIngrediente[quantidade][]" class="form-control" step="0.001"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-remover btn-responsive">Remover</button></td>
    </tr>
</template>

<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/receita/js/_form.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
