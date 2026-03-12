<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Unidades de Medida';
?>

<div class="unidade-medida-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Nova Unidade', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-unidade" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-unidade" class="form-control" placeholder="Filtrar por nome, sigla ou categoria...">
            <small class="text-muted">Clique no nome das colunas para ordenar.</small>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-unidades'],
        'columns' => [
            'id',
            'nome',
            'sigla',
            'categoria',
            ['attribute' => 'fator_base', 'format' => ['decimal', 4]],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update} {delete}',
                'buttons' => [],
            ],
        ],
    ]) ?>
</div>

<?php
$this->registerJsFile('@web/views/unidade-medida/js/index.js.php', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
