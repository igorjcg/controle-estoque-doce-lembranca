<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UnidadeMedida $model */

$this->title = 'Nova Unidade de Medida';
?>

<div class="unidade-medida-create">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
