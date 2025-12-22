<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = '编辑客户：' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->CustomerID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="customer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
