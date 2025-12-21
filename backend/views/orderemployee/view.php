<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => 'Order Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'OrderID',
            'EmployeeID',
        ],
    ]) ?>

</div>
