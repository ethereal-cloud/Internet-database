<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = 'Update Order Employee: ' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => 'Order Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->OrderID, 'url' => ['view', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
