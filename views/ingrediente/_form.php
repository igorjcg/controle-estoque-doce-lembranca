<?php

use app\models\UnidadeMedida;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Ingrediente $model */

$unidadesList = UnidadeMedida::find()->orderBy(['nome' => SORT_ASC])->all();
$unidades = ArrayHelper::map($unidadesList, 'id', static fn($item) => $item->nome . ' (' . $item->sigla . ')');
$unidadesSiglas = ArrayHelper::map($unidadesList, 'id', 'sigla');
?>

<div class="ingrediente-form" data-unidades-siglas='<?= Json::htmlEncode($unidadesSiglas) ?>'>
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'unidade_medida_id')->dropDownList($unidades, ['prompt' => 'Selecione']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php
            $labelEstoqueMin = 'Estoque minimo para alerta';
            if (isset($model->unidadeMedida) && $model->unidadeMedida) {
                $labelEstoqueMin .= ' (em ' . Html::encode($model->unidadeMedida->sigla) . ')';
            }
            echo $form->field($model, 'estoque_minimo_alerta', ['labelOptions' => ['id' => 'label-estoque-minimo']])
                ->textInput(['type' => 'number', 'step' => '0.0001', 'id' => 'ingrediente-estoque_minimo_alerta'])
                ->label($labelEstoqueMin)
                ->hint('Sempre na unidade base do ingrediente. Ex.: se a unidade for kg, use 5 para 5 kg ou 0,2 para 200 g.');
            ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/ingrediente/js/_form.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
