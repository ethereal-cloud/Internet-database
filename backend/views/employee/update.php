<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */

$this->title = '编辑员工：' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => '员工列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->EmployeeID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="employee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
