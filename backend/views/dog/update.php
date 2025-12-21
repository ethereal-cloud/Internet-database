<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dog */

$this->title = 'Update Dog: ' . $model->PetID;
$this->params['breadcrumbs'][] = ['label' => 'Dogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PetID, 'url' => ['view', 'id' => $model->PetID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="dog-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
