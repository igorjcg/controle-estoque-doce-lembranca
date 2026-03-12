<?php

use app\common\util\Util;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Produções';
?>

<div class="producao-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Registrar Producao', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-producao" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-producao" class="form-control" placeholder="Filtrar por receita ou observação...">
            <small class="text-muted">A grade já permite ordenacao pelo cabeçalho.</small>
        </div>
    </div>

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
            'observacao:ntext',
            ['attribute' => 'created_at', 'format' => 'datetime'],
        ],
    ]) ?>
</div>

<?php
$jsUrl = Yii::$app->assetManager->publish('@app/views/producao/js/index.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
