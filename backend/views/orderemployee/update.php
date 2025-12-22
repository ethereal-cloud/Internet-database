<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = '编辑关联：' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => '订单-员工', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->OrderID, 'url' => ['view', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="order-employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
