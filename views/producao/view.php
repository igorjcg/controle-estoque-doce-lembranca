<?php

use app\common\util\Util;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Producao $model */

$this->title = 'Produção #' . $model->id;
?>

<div class="producao-view">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="page-actions">
            <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-responsive']) ?>
            <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-responsive',
                'data' => [
                    'confirm' => 'Tem certeza que deseja excluir este item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Receita',
                'value' => $model->receita->nome ?? '-',
            ],
            [
                'attribute' => 'quantidade',
                'value' => Util::formatDecimalTrimmed((float) $model->quantidade),
            ],
            [
                'attribute' => 'custo_unitario',
                'value' => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_unitario, 2),
            ],
            [
                'attribute' => 'custo_total',
                'value' => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_total, 2),
            ],
            'observacao:ntext',
            'created_at:datetime',
            [
                'label' => 'Usuário',
                'value' => $model->criadoPor->username ?? '-',
            ],
        ],
    ]) ?>
</div>
