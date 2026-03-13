<?php

use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Receitas';
?>

<div class="receita-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Nova Receita', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label for="filtro-receita" class="form-label fw-semibold mb-1">Filtro rapido</label>
            <input type="text" id="filtro-receita" class="form-control" placeholder="Filtrar por nome ou descrição...">
            <small class="text-muted">Ordene pelos cabecalhos da grade.</small>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'pjax-receitas']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'tabela-receitas'],
        'columns' => [
            'id',
            'nome',
            [
                'attribute' => 'descricao',
                'value' => static function ($model) {
                    $descricao = (string)$model->descricao;
                    return mb_strlen($descricao) > 80 ? mb_substr($descricao, 0, 80) . '...' : $descricao;
                },
            ],
            [
                'label' => 'Utilizar',
                'format' => 'raw',
                'value' => static function ($model) {
                    return Html::button('Utilizar', [
                        'type' => 'button',
                        'class' => 'btn btn-sm btn-primary btn-utilizar-receita',
                        'data-id' => $model->id,
                        'data-nome' => $model->nome,
                        'data-url' => Url::to(['receita/utilizar']),
                    ]);
                },
                'headerOptions' => ['style' => 'width:110px'],
                'contentOptions' => ['style' => 'text-align:center'],
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['style' => 'width:80px'],
                'contentOptions' => ['style' => 'text-align:center'],
            ]
        ],
    ]) ?>
    <?php Pjax::end(); ?>
</div>

<?php
$this->registerCssFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
$jsUrl = Yii::$app->assetManager->publish('@app/views/receita/js/index.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);
?>
