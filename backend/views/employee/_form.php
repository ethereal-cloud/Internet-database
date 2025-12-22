<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */
/* @var $form yii\widgets\ActiveForm */

$user = Yii::$app->user->identity;
$isAdmin = $user && $user->role === 'admin';
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'EmployeeID')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('员工编号（自动生成）') ?>

    <?= $form->field($model, 'user_id')->textInput(['readonly' => true, 'style' => 'background-color: #f5f5f5;'])->label('用户ID（自动生成）') ?>

    <?= $form->field($model, 'Name')->textInput(['maxlength' => true, 'readonly' => !$isAdmin, 'style' => !$isAdmin ? 'background-color: #f5f5f5;' : ''])->label('姓名') ?>

    <?= $form->field($model, 'Gender')->dropDownList([ '男' => '男', '女' => '女', ], ['prompt' => '选择性别', 'disabled' => !$isAdmin, 'style' => !$isAdmin ? 'background-color: #f5f5f5;' : ''])->label('性别') ?>

    <?= $form->field($model, 'Position')->textInput(['maxlength' => true, 'readonly' => !$isAdmin, 'style' => !$isAdmin ? 'background-color: #f5f5f5;' : ''])->label('职位') ?>

    <?= $form->field($model, 'Contact')->textInput(['maxlength' => true])->label('联系方式') ?>

    <?= $form->field($model, 'HireDate')->input('date', [
        'readonly' => !$isAdmin,
        'style' => !$isAdmin ? 'background-color: #f5f5f5;' : '',
        'value' => $model->HireDate ? date('Y-m-d', strtotime($model->HireDate)) : '',
    ])->label('入职日期') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
