<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Ingrediente $model */

$this->title = 'Novo Ingrediente';
?>

<div class="ingrediente-create">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
