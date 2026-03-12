<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MovimentacaoEstoque $model */

$this->title = 'Entrada de Estoque';
?>

<div class="movimentacao-estoque-entrada">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
