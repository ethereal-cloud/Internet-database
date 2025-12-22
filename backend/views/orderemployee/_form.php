<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderEmployee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'OrderID')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('订单编号') ?>

    <?= $form->field($model, 'EmployeeID')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('员工编号') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
