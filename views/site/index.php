<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var int $totalIngredientes */
/** @var int $totalEstoqueBaixo */
/** @var float $valorTotalEstoque */
/** @var int $totalMovimentacoesHoje */
/** @var int $totalProducoesRegistradas */
/** @var app\models\Ingrediente[] $ingredientesEstoqueBaixo */
/** @var array<string, mixed> $graficoMenorEstoque */
/** @var array<string, mixed> $graficoMovimentacoes */
/** @var array<string, mixed> $graficoDistribuicao */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Controle de Estoque Doce Lembrança';

$cardsMetricas = [
    [
        'titulo' => 'Total de Ingredientes',
        'valor' => (int) $totalIngredientes,
        'descricao' => 'Ingredientes ativos cadastrados',
        'classe' => 'text-primary',
        'icone' => 'bi-box-seam',
    ],
    [
        'titulo' => 'Estoque Baixo',
        'valor' => (int) $totalEstoqueBaixo,
        'descricao' => 'Itens que exigem reposição',
        'classe' => 'text-danger',
        'icone' => 'bi-exclamation-triangle',
    ],
    [
        'titulo' => 'Valor Total do Estoque',
        'valor' => Yii::$app->formatter->asCurrency($valorTotalEstoque, 'BRL'),
        'descricao' => 'Baseado no custo médio atual',
        'classe' => 'text-success',
        'icone' => 'bi-cash-stack',
    ],
    [
        'titulo' => 'Movimentações Hoje',
        'valor' => (int) $totalMovimentacoesHoje,
        'descricao' => 'Entradas e saídas registradas hoje',
        'classe' => 'text-warning',
        'icone' => 'bi-arrow-left-right',
    ],
    [
        'titulo' => 'Produções Registradas',
        'valor' => (int) $totalProducoesRegistradas,
        'descricao' => 'Total histórico de produções',
        'classe' => 'text-info',
        'icone' => 'bi-clipboard-data',
    ],
];

$chartPayload = [
    'menorEstoque' => $graficoMenorEstoque,
    'movimentacoes' => $graficoMovimentacoes,
    'distribuicao' => $graficoDistribuicao,
];

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', [
    'depends' => [JqueryAsset::class],
]);
$this->registerJsVar('dashboardChartData', $chartPayload);

$this->registerCss(<<<CSS
.dashboard-hero {
    background: linear-gradient(135deg, #fff8f0 0%, #f7efe6 55%, #f2dfcf 100%);
    border: 1px solid rgba(146, 111, 83, 0.12);
}

.metric-card {
    border: 0;
    box-shadow: 0 0.5rem 1.5rem rgba(40, 29, 20, 0.08);
}

.metric-icon {
    width: 3rem;
    height: 3rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.72);
    font-size: 1.25rem;
}

.chart-panel {
    border: 0;
    box-shadow: 0 0.5rem 1.5rem rgba(40, 29, 20, 0.08);
}

.chart-canvas-wrap {
    position: relative;
    min-height: 320px;
}

.chart-canvas-wrap.chart-canvas-wrap-sm {
    min-height: 280px;
}

@media (max-width: 767.98px) {
    .chart-canvas-wrap,
    .chart-canvas-wrap.chart-canvas-wrap-sm {
        min-height: 260px;
    }
}
CSS);

$jsUrl = Yii::$app->assetManager->publish('@app/views/site/js/index.js.php')[1];
$this->registerJsFile($jsUrl, [
    'depends' => [JqueryAsset::class],
]);

?>

<div class="dashboard-index">

    <?php if ($totalEstoqueBaixo > 0): ?>
        <div class="alert alert-danger d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4" role="alert">
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
                <?= Html::a('Ver lista de estoque baixo', ['/ingrediente/estoque-baixo'], ['class' => 'btn btn-outline-danger btn-responsive mt-3 mt-md-2']) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="dashboard-hero rounded-4 p-4 p-lg-5 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="text-uppercase text-muted fw-semibold small mb-2">Painel Analítico</p>
                <h1 class="mb-2"><?= Html::encode($this->title) ?></h1>
                <p class="text-muted mb-0">Acompanhe estoque, movimentações e produção em um único painel.</p>
            </div>
            <div class="page-actions">
                <?= Html::a('+ Entrada de estoque', ['/movimentacao-estoque/entrada'], ['class' => 'btn btn-success btn-responsive']) ?>
                <?= Html::a('- Saida de estoque', ['/movimentacao-estoque/saida'], ['class' => 'btn btn-danger btn-responsive']) ?>
                <?= Html::a('+ Registrar produção', ['/producao/create'], ['class' => 'btn btn-primary btn-responsive']) ?>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <?php foreach ($cardsMetricas as $card): ?>
            <div class="col-12 col-md-6 col-xl">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <p class="text-muted text-uppercase small fw-semibold mb-2"><?= Html::encode($card['titulo']) ?></p>
                                <div class="h2 fw-bold mb-1 <?= Html::encode($card['classe']) ?>"><?= Html::encode((string) $card['valor']) ?></div>
                                <small class="text-muted"><?= Html::encode($card['descricao']) ?></small>
                            </div>
                            <span class="metric-icon <?= Html::encode($card['classe']) ?>">
                                <i class="bi <?= Html::encode($card['icone']) ?>"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card chart-panel h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-1">Ingredientes com menor estoque</h2>
                    <p class="text-muted small mb-0">Os 10 ingredientes mais próximos do esgotamento.</p>
                </div>
                <div class="card-body pt-2 px-4 pb-4">
                    <div class="chart-canvas-wrap">
                        <canvas id="chart-menor-estoque"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card chart-panel h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-1">Movimentações nos últimos 7 dias</h2>
                    <p class="text-muted small mb-0">Entradas e saídas por dia para leitura rápida de tendência.</p>
                </div>
                <div class="card-body pt-2 px-4 pb-4">
                    <div class="chart-canvas-wrap">
                        <canvas id="chart-movimentacoes"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-5">
            <div class="card chart-panel h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-1">Distribuição do estoque</h2>
                    <p class="text-muted small mb-0">Participação do valor em estoque por ingrediente.</p>
                </div>
                <div class="card-body pt-2 px-4 pb-4">
                    <div class="chart-canvas-wrap chart-canvas-wrap-sm">
                        <canvas id="chart-distribuicao"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-7">
            <div class="card chart-panel h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-1">Leitura operacional</h2>
                    <p class="text-muted small mb-0">Cruze os indicadores acima com a grade para decidir reposição, produção e compras.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 bg-light p-3 h-100">
                                <p class="small text-uppercase text-muted fw-semibold mb-2">Recomendação</p>
                                <p class="mb-0">Priorize compras dos itens que aparecem no gráfico de menor estoque e também no alerta vermelho.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 bg-light p-3 h-100">
                                <p class="small text-uppercase text-muted fw-semibold mb-2">Movimentação</p>
                                <p class="mb-0">Use a curva dos últimos 7 dias para identificar picos de consumo e ajustar o ritmo de reposição.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="rounded-4 border p-3 h-100">
                                <p class="small text-uppercase text-muted fw-semibold mb-2">Valor do estoque</p>
                                <p class="mb-2">O doughnut considera o valor do estoque por ingrediente com base no custo médio cadastrado.</p>
                                <p class="mb-0 text-muted small">Para manter o gráfico legível, os itens de menor participação são agrupados em <strong>Outros</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
-->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Quantidade atual por ingrediente</strong>
            <?= Html::a('Cadastrar ingrediente', ['/ingrediente/create'], ['class' => 'btn btn-sm btn-success btn-responsive']) ?>
        </div>
        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'dashboard-grid']); ?>
            <div class="table-responsive">
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
            </div>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
