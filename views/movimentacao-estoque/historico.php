<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Histórico de Movimentações';
?>

<div class="movimentacao-estoque-historico">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="d-flex gap-2">
            <?= Html::a('Entrada', ['entrada'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Saida', ['saida'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-historico" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-historico" class="form-control" placeholder="Digite ingrediente ou tipo de movimento...">
            <small class="text-muted">Use o cabecalho da tabela para ordenar.</small>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-historico'],
        'columns' => [
            [
                'label' => '#',
                'value' => 'id'
            ],
            [
                'label' => 'Ingrediente',
                'value' => static function ($model) {
                    return $model->ingrediente->nome ?? '-';
                },
            ],
            [
                'attribute' => 'tipo_movimento',
                'format' => 'raw',
                'value' => static function ($model) {
                    return $model->tipo_movimento === 'entrada'
                        ? '<span class="badge bg-success">entrada</span>'
                        : '<span class="badge bg-danger">saida</span>';
                },
            ],
            [
                'attribute' => 'quantidade',
                'value' => static function ($model) {
                    $quantidade = Yii::$app->formatter->asInteger($model->quantidade);
                    $sigla = $model->ingrediente->unidadeMedida->sigla ?? '';

                    return trim($quantidade . ' ' . $sigla);
                },
            ],
            [
                'attribute' => 'valor_unitario',
                'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->valor_unitario, 2),
            ],
            [
                'attribute' => 'valor_total',
                'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->valor_total, 2),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => 'criadoPor.username',
            ],
        ],
    ]) ?>
</div>

<?php
$this->registerJsFile('@web/views/movimentacao-estoque/js/historico.js.php', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
