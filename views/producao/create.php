<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Producao $model */

$this->title = 'Registrar Produção';
?>

<div class="producao-create">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
