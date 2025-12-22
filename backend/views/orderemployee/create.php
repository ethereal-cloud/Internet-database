<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = '新增订单-员工关联';
$this->params['breadcrumbs'][] = ['label' => '订单-员工', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
