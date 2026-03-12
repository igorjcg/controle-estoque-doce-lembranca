<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Receita $model */
/** @var app\models\ReceitaIngrediente[] $ingredientesReceita */

$this->title = 'Receita: ' . $model->nome;
?>

<div class="receita-view">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="d-flex gap-2">
            <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Confirma a exclusao desta receita?',
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
            'descricao:ntext',
        ],
    ]) ?>

    <div class="card mt-4">
        <div class="card-header bg-white">
            <strong>Ingredientes da Receita</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Ingrediente</th>
                        <th>Unidade</th>
                        <th>Quantidade</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ingredientesReceita)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-3">Nenhum ingrediente cadastrado para esta receita.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ingredientesReceita as $item): ?>
                            <tr>
                                <td><?= Html::encode($item->ingrediente->nome ?? '-') ?></td>
                                <td><?= Html::encode(number_format($item->quantidade, 0, ',', '')) ?></td>
                                <td><?= Html::encode($item->unidadeMedida->sigla ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
