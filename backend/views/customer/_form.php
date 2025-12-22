<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'CustomerID')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('客户编号（自动生成）') ?>

    <?= $form->field($model, 'user_id')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('用户ID（自动生成）') ?>

    <?= $form->field($model, 'Name')->textInput(['maxlength' => true])->label('姓名') ?>

    <?= $form->field($model, 'Gender')->dropDownList([ '男' => '男', '女' => '女', ], ['prompt' => '选择性别'])->label('性别') ?>

    <?= $form->field($model, 'Contact')->textInput(['maxlength' => true])->label('联系方式') ?>

    <?= $form->field($model, 'Address')->textInput(['maxlength' => true])->label('地址') ?>

    <?= $form->field($model, 'MemberLevel')->dropDownList([ '普通会员' => '普通会员', '银卡会员' => '银卡会员', '金卡会员' => '金卡会员', ], ['prompt' => '选择会员等级'])->label('会员等级') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
