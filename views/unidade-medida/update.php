<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UnidadeMedida $model */

$this->title = 'Atualizar Unidade de Medida: ' . $model->nome;
?>

<div class="unidade-medida-update">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
