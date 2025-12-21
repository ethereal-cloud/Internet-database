<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */

$this->title = 'Create Order Employee';
$this->params['breadcrumbs'][] = ['label' => 'Order Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
