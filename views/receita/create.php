<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Receita $model */

$this->title = 'Nova Receita';
?>

<div class="receita-create">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'ingredientesReceita' => [],
    ]) ?>
</div>
