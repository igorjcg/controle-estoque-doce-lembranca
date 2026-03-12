<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Ingrediente $model */

$this->title = 'Atualizar Ingrediente: ' . $model->nome;
?>

<div class="ingrediente-update">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
