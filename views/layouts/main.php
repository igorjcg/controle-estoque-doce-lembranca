<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCssFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
$confirmJsUrl = Yii::$app->assetManager->publish('@app/views/layouts/js/yii-confirm.js.php')[1];
$this->registerJsFile($confirmJsUrl, [
    'depends' => [\yii\web\JqueryAsset::class],
]);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/img/logoAntiga.jpg', [
            'alt' => Yii::$app->name,
            'style' => 'height:50px; width:auto;'
        ]),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-expand-md navbar-dark fixed-top',
            'style' => 'background-color:#926f53;'
        ]
    ]);

    $menuItems = [];
    $authItems = [];

    if (Yii::$app->user->isGuest) {
        $authItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems = [
            ['label' => 'Ingredientes', 'url' => ['/ingrediente/index']],
            ['label' => 'Receitas', 'url' => ['/receita/index']],
            ['label' => 'Medidas', 'url' => ['/unidade-medida/index']],
            ['label' => 'Movimentações', 'url' => ['/movimentacao-estoque/historico']],
            ['label' => 'Produção', 'url' => ['/producao/index']],
            [
                'label' => 'Cadastrar usuário',
                'url' => '#',
                'linkOptions' => [
                    'id' => 'gerar-convite-usuario',
                    'data-url' => Url::to(['/usuario/gerar-convite']),
                ],
            ],
        ];

        $authItems[] = [
            'label' => 'Olá ' . Yii::$app->user->identity->username,
            'items' => [
                [
                    'label' => 'Sair',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                ],
            ],
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $authItems,
    ]);

    NavBar::end();
    ?>
</header>

<?php if (!Yii::$app->user->isGuest): ?>
    <?php Modal::begin([
        'id' => 'convite-usuario-modal',
        'title' => 'Link para cadastro de usuário',
    ]); ?>
    <p>Copie o link abaixo (Ctrl+C) e envie para a pessoa que você deseja cadastrar no sistema.</p>
    <div class="input-group">
        <input type="text" class="form-control" id="convite-usuario-link" readonly>
        <button type="button" class="btn btn-outline-primary" id="copiar-convite-usuario">Copiar link</button>
    </div>
    <?php Modal::end(); ?>

    <?php
    $conviteJs = <<<'JS'
const botaoGerarConvite = document.getElementById('gerar-convite-usuario');
const modalConvite = document.getElementById('convite-usuario-modal');
const campoConvite = document.getElementById('convite-usuario-link');
const botaoCopiarConvite = document.getElementById('copiar-convite-usuario');

if (botaoGerarConvite && modalConvite && campoConvite && botaoCopiarConvite) {
    const instanciaModal = bootstrap.Modal.getOrCreateInstance(modalConvite);

    botaoGerarConvite.addEventListener('click', async (event) => {
        event.preventDefault();

        try {
            const response = await fetch(botaoGerarConvite.dataset.url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': yii.getCsrfToken(),
                },
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Não foi possível gerar o convite.');
            }

            campoConvite.value = data.link;
            instanciaModal.show();
        } catch (error) {
            window.alert(error.message);
        }
    });

    botaoCopiarConvite.addEventListener('click', async () => {
        if (!campoConvite.value) {
            return;
        }

        campoConvite.focus();
        campoConvite.select();

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(campoConvite.value);
            } else {
                const copiou = document.execCommand('copy');
                if (!copiou) {
                    throw new Error('Falha ao copiar o link.');
                }
            }
        } catch (error) {
            const copiou = document.execCommand('copy');
            if (!copiou) {
                window.alert('Nao foi possivel copiar o link automaticamente. Use Ctrl+C.');
            }
        }
    });

    modalConvite.addEventListener('shown.bs.modal', () => {
        campoConvite.focus();
        campoConvite.select();
    });
}
JS;
    $this->registerJs($conviteJs);
    ?>
<?php endif; ?>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


