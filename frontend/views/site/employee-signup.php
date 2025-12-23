<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
/* @var $inviteCode string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '员工注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>请填写以下信息注册员工账号（需要邀请码）：</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-employee-signup']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '用户名']) ?>

                <?= $form->field($model, 'email')->textInput(['placeholder' => '邮箱地址']) ?>

                <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码（至少6位）']) ?>
                
                <hr>
                <h4>员工信息</h4>

                <?= $form->field($model, 'employeeName')->textInput(['placeholder' => '真实姓名'])->label('姓名') ?>

                <?= $form->field($model, 'employeeGender')->dropDownList(
                    ['男' => '男', '女' => '女'],
                    ['prompt' => '请选择性别']
                )->label('性别') ?>

                <?= $form->field($model, 'employeePosition')->textInput(['placeholder' => '例如：护理员、兽医、宠物行为训练师'])->label('职位') ?>

                <?= $form->field($model, 'employeeContact')->textInput(['placeholder' => '手机号码或其他联系方式'])->label('联系方式') ?>
                
                <hr>

                <?= $form->field($model, 'inviteCode')->textInput(['placeholder' => '请输入邀请码'])->label('邀请码') ?>

                <div class="form-group">
                    <?= Html::submitButton('注册员工账号', ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
            
            <p class="text-muted">
                <small>提示：员工邀请码请向管理员索取</small>
            </p>
            
            <p>
                <?= Html::a('返回普通注册', ['signup'], ['class' => 'btn btn-link']) ?>
                <?= Html::a('已有账号？去登录', ['login'], ['class' => 'btn btn-link']) ?>
            </p>
        </div>
    </div>
</div>
