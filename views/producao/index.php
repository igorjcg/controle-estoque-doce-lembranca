<?php

use app\common\util\Util;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Produções';
?>

<div class="producao-index">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="page-actions">
            <?= Html::a('Análise de Produção', ['analise'], ['class' => 'btn btn-outline-primary btn-responsive']) ?>
            <?= Html::a('Registrar Produção', ['create'], ['class' => 'btn btn-success btn-responsive']) ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-producao" class="form-label fw-semibold mb-1">Filtro rápido</label>
            <input type="text" id="filtro-producao" class="form-control" placeholder="Filtrar por receita ou usuário...">
            <small class="text-muted">A grade já permite ordenação pelo cabeçalho.</small>
        </div>
    </div>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-producoes'],
        'columns' => [
            'id',
            [
                'label' => 'Receita',
                'value' => static function ($model) {
                    return $model->receita->nome ?? '-';
                },
            ],
            [
                'attribute' => 'quantidade',
                'value' => static function ($model) {
                    return Util::formatDecimalTrimmed((float) $model->quantidade);
                },
            ],
            [
                'attribute' => 'custo_unitario',
                'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_unitario, 2),
            ],
            [
                'attribute' => 'custo_total',
                'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_total, 2),
            ],
            ['attribute' => 'created_at', 'format' => 'datetime'],
            [
                'label' => 'Usuário',
                'value' => static fn($model) => $model->criadoPor->username ?? '-',
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]) ?>
    </div>
</div>

<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/producao/js/index.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
