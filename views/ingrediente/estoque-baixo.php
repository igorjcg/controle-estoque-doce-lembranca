<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Ingredientes com Estoque Baixo';
?>

<div class="ingrediente-estoque-baixo">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary btn-responsive']) ?>
    </div>

    <div class="alert alert-warning py-2">
        Itens desta lista estao em estado critico.
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-estoque-baixo" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-estoque-baixo" class="form-control" placeholder="Filtrar por ingrediente ou unidade...">
        </div>
    </div>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-estoque-baixo'],
        'columns' => [
            'id',
            'nome',
            [
                'label' => 'Estoque Atual',
                'value' => static function ($model) {
                    return $model->getEstoqueAtualFormatado();
                },
            ],
            [
                'attribute' => 'estoque_minimo_alerta',
                'value' => static function ($model) {
                    return $model->getEstoqueMinimoAlertaFormatado();
                },
            ],
            [
                'label' => 'Unidade',
                'value' => static function ($model) {
                    return $model->unidadeMedida->sigla ?? '-';
                },
            ],
            [
                'label' => 'Status',
                'format' => 'raw',
                'value' => '<span class="badge bg-danger">Estoque baixo</span>',
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update}',
            ],
        ],
    ]) ?>
    </div>
</div>

<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/ingrediente/js/estoque-baixo.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
