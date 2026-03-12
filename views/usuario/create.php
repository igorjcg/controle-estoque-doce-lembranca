<?php

/** @var yii\web\View $this */
/** @var app\models\User $model */

use yii\helpers\Html;

$this->title = 'Criar Usuario';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['create']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="usuario-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
