<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */

$this->title = '编辑订单：' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->OrderID, 'url' => ['view', 'id' => $model->OrderID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="fosterorder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
