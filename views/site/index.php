<?php

use app\common\util\Util;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var int $totalIngredientes */
/** @var int $totalEstoqueBaixo */
/** @var app\models\Ingrediente[] $ingredientesEstoqueBaixo */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Controle de Estoque Doce Lembrança';

?>

<div class="dashboard-index">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('+ Entrada de estoque', ['/movimentacao-estoque/entrada'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('- Saida de estoque', ['/movimentacao-estoque/saida'], ['class' => 'btn btn-danger']) ?>
            <?= Html::a('+ Registrar produção', ['/producao/create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php if ($totalEstoqueBaixo > 0): ?>
    <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4" role="alert">
        <div>
            <strong>Atenção:</strong>
            <ul class="mb-0 mt-2 ps-3">
                <?php foreach ($ingredientesEstoqueBaixo as $ingrediente): ?>
                    <?php
                    $estoqueAtual = Html::encode($ingrediente->estoqueAtualComUnidadeFormatado);
                    $minimo = Html::encode($ingrediente->estoqueMinimoAlertaComUnidadeFormatado);
                    ?>
                    <li>
                        <strong><?= Html::encode($ingrediente->nome) ?>:</strong>
                        estoque atual em <?= $estoqueAtual ?>, abaixo do mínimo de <?= $minimo ?>.
                    </li>
                <?php endforeach; ?>
            </ul>
            <?= Html::a('Ver lista de estoque baixo ->', ['/ingrediente/estoque-baixo'], ['class' => 'alert-link ms-2']) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total de Ingredientes</h6>
                            <p class="display-6 mb-0 fw-bold"><?= (int) $totalIngredientes ?></p>
                        </div>
                        <span class="text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.5.134a1 1 0 0 0-1 0l-6 3.5A1 1 0 0 0 1 4.5v7a1 1 0 0 0 .5.866l6 3.5a1 1 0 0 0 1 0l6-3.5a1 1 0 0 0 .5-.866v-7a1 1 0 0 0-.5-.866zM8 1.293 13.5 4.5 8 7.707 2.5 4.5zM2 5.366l5.5 3.207v6.134L2 11.5zm11 0V11.5l-5.5 3.207V8.573z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 <?= $totalEstoqueBaixo > 0 ? 'border-danger' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Ingredientes com Estoque Baixo</h6>
                            <p class="display-6 mb-0 fw-bold text-danger"><?= (int) $totalEstoqueBaixo ?></p>
                        </div>
                        <span class="text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.063 2h.874c.09 0 .17.048.215.126l6.857 11.856c.092.159-.02.358-.215.358H.206c-.194 0-.306-.199-.215-.358zm.562 4.984a.905.905 0 1 0-1.81 0l.35 3.507a.552.552 0 0 0 1.11 0zm-.002 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                            </svg>
                        </span>
                    </div>
                    <?php if ($totalEstoqueBaixo > 0): ?>
                    <div class="mt-2">
                        <?= Html::a('Ver lista ->', ['/ingrediente/estoque-baixo'], ['class' => 'btn btn-sm btn-outline-danger']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Resumo em estoque</h6>
                            <p class="display-6 mb-0 fw-bold"><?= (int) $totalIngredientes ?></p>
                            <small class="text-muted">itens no grid abaixo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Quantidade atual por ingrediente</strong>
            <?= Html::a('Cadastrar ingrediente', ['/ingrediente/create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
        <div class="card-body p-0">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'layout' => "{items}\n{pager}",
                'emptyText' => 'Nenhum ingrediente cadastrado.',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'nome',
                        'label' => 'Ingrediente',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::encode($model->nome);
                        },
                    ],
//                     [
//                         'label' => 'Unidade',
//                         'value' => function ($model) {
//                             return Html::encode($model->unidadeMedida->sigla ?? '-');
//                         },
//                     ],
                    [
                        'label' => 'Quantidade atual',
                        'value' => 'estoqueAtualComUnidadeFormatado',
                    ],
                    [
                        'attribute' => 'estoque_minimo_alerta',
                        'value' => 'estoqueMinimoAlertaComUnidadeFormatado',
                    ],
                    [
                        'label' => 'Status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $minimo = (float) $model->estoque_minimo_alerta;
                            $estaBaixo = $minimo > 0 && $model->getEstoqueAtual() <= $minimo;
                            return $estaBaixo
                                ? '<span class="badge bg-danger">Estoque baixo</span>'
                                : '<span class="badge bg-success">OK</span>';
                        },
                    ],
                ],
            ]) ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
