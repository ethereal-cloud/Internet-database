<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterorder-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'OrderID')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('订单编号（自动生成）') ?>

    <?= $form->field($model, 'CustomerID')->textInput()->label('客户编号') ?>

    <?= $form->field($model, 'PetID')->textInput()->label('宠物编号') ?>

    <?= $form->field($model, 'ServiceID')->textInput()->label('服务编号') ?>

    <?= $form->field($model, 'StartTime')->textInput()->label('开始时间') ?>

    <?= $form->field($model, 'EndTime')->textInput()->label('结束时间') ?>

    <?= $form->field($model, 'OrderStatus')->dropDownList([ '未支付' => '未支付', '已支付' => '已支付', ], ['prompt' => '选择状态'])->label('订单状态') ?>

    <?= $form->field($model, 'PaymentAmount')->textInput(['maxlength' => true])->label('支付金额') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
