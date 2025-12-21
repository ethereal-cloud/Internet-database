<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */

$this->title = 'Update Fosterorder: ' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => 'Fosterorders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->OrderID, 'url' => ['view', 'id' => $model->OrderID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fosterorder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
