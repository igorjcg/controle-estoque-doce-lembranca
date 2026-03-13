<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Ingrediente $model */

$this->title = 'Ingrediente: ' . $model->nome;
?>

<div class="ingrediente-view">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="page-actions">
            <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-responsive']) ?>
            <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-responsive',
                'data' => [
                    'confirm' => 'Confirma a exclusao deste ingrediente?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            [
                'label' => 'Unidade',
                'value' => $model->unidadeMedida->nome . ' (' . $model->unidadeMedida->sigla . ')',
            ],
            [
                'label' => 'Estoque Atual',
                'value' => $model->getEstoqueAtualFormatado(),
            ],
            ['attribute' => 'estoque_minimo_alerta', 'value' => $model->getEstoqueMinimoAlertaFormatado()],
            [
                'label' => 'Custo Médio',
                'value' => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model->custo_medio, 2),
            ],
            [
                'label' => 'Status do Estoque',
                'format' => 'raw',
                'value' => $model->getEstoqueAtual() <= (float) $model->estoque_minimo_alerta
                    ? '<span class="badge bg-danger">Estoque baixo</span>'
                    : '<span class="badge bg-success">OK</span>',
            ],
        ],
    ]) ?>
</div>
