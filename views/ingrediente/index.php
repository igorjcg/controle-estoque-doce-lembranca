<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\common\util\Util;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ingredientes';
//$primeiroIngrediente = $dataProvider->getModels()[3] ?? null;
?>

<div class="ingrediente-index">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="page-actions">
            <?= Html::a('Estoque Baixo', ['estoque-baixo'], ['class' => 'btn btn-outline-warning btn-responsive']) ?>
            <?= Html::a('Novo Ingrediente', ['create'], ['class' => 'btn btn-success btn-responsive']) ?>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-ingrediente" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-ingrediente" class="form-control" placeholder="Digite nome, unidade ou status...">
            <small class="text-muted">A ordenação pode ser feita clicando no cabeçalho das colunas</small>
        </div>
    </div>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-ingredientes'],
        'columns' => [
            'id',
            'nome',
            [
                'label' => 'Estoque Atual',
                'value' => 'estoqueAtualComUnidadeFormatado',
            ],
            [
                'attribute' => 'estoque_minimo_alerta',
                'value' => 'estoqueMinimoAlertaComUnidadeFormatado',
            ],
            [
                'label' => 'Custo Médio',
                'value' => static function ($model) {
                    return 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_medio, 2);
                },
                'format' => 'raw',
            ],
//             [
//                 'attribute' => 'unidade_medida_id',
//                 'label' => 'Unidade',
//                 'value' => static function ($model) {
//                     return $model->unidadeMedida->sigla ?? $model->unidade_medida_id;
//                 },
//             ],
            [
                'label' => 'Status',
                'format' => 'raw',
                'value' => static function ($model) {
                    if ($model->getEstoqueAtual() <= (float) $model->estoque_minimo_alerta) {
                        return '<span class="badge bg-danger">Estoque baixo</span>';
                    }
                    return '<span class="badge bg-success">OK</span>';
                },
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'buttons' => [],
            ],
        ],
    ]) ?>
    </div>
</div>

<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/ingrediente/js/index.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
