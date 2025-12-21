<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dog */

$this->title = 'Create Dog';
$this->params['breadcrumbs'][] = ['label' => 'Dogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dog-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
