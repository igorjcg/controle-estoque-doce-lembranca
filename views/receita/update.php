<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Receita $model */
/** @var app\models\ReceitaIngrediente[] $ingredientesReceita */

$this->title = 'Atualizar Receita: ' . $model->nome;
?>

<div class="receita-update">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'ingredientesReceita' => $ingredientesReceita,
    ]) ?>
</div>
