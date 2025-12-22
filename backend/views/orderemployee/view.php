<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = '关联详情：' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => '订单-员工', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该关联？',
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
