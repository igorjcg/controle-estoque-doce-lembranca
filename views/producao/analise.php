<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var float $custoTotalMes */
/** @var float $valorEstoqueAtual */
/** @var string $lucroEstimadoFormula */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Análise de Produção';
?>

<div class="producao-analise">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Custo total produzido no mês</h6>
                    <p class="display-6 mb-0"><?= 'R$ ' . Yii::$app->formatter->asDecimal($custoTotalMes, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Valor do estoque atual</h6>
                    <p class="display-6 mb-0"><?= 'R$ ' . Yii::$app->formatter->asDecimal($valorEstoqueAtual, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Lucro estimado</h6>
                    <p class="mb-0"><?= Html::encode($lucroEstimadoFormula) ?></p>
                    <small class="text-muted">Estrutura preparada para integração futura com preço de venda.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <strong>Valor do estoque por ingrediente</strong>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-bordered mb-0'],
                'columns' => [
                    ['attribute' => 'nome', 'label' => 'Ingrediente'],
                    ['attribute' => 'estoque_atual', 'label' => 'Estoque Atual'],
                    [
                        'attribute' => 'custo_medio',
                        'label' => 'Custo Médio',
                        'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model['custo_medio'], 2),
                    ],
                    [
                        'attribute' => 'valor_total',
                        'label' => 'Valor em Estoque',
                        'value' => static fn($model) => 'R$ ' . Yii::$app->formatter->asDecimal((float) $model['valor_total'], 2),
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
