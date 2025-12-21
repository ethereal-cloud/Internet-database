<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '客户登录';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.customer-login {
    background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -20px;
    padding: 20px;
}
.customer-login-box {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-width: 400px;
    width: 100%;
}
.customer-login-box h1 {
    color: #66a6ff;
    text-align: center;
    margin-bottom: 10px;
    font-weight: bold;
}
.customer-login-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
    font-size: 14px;
}
.customer-login-icon {
    text-align: center;
    font-size: 60px;
    color: #66a6ff;
    margin-bottom: 20px;
}
.btn-customer-login {
    background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
    border: none;
    width: 100%;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    color: white;
}
.btn-customer-login:hover {
    background: linear-gradient(135deg, #66a6ff 0%, #89f7fe 100%);
    color: white;
}
.customer-links {
    text-align: center;
    margin-top: 20px;
}
.customer-links a {
    color: #66a6ff;
    margin: 0 10px;
}
</style>

<div class="customer-login">
    <div class="customer-login-box">
        <div class="customer-login-icon">
            <i class="glyphicon glyphicon-user"></i>
        </div>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="customer-login-subtitle">🐾 欢迎来到宠物寄养平台</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '用户名'])->label('账号') ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码'])->label('密码') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('记住我') ?>

            <div style="color:#999;margin:1em 0; font-size: 12px;">
                忘记密码？<?= Html::a('重置密码', ['site/request-password-reset']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> 登录', ['class' => 'btn btn-success btn-customer-login', 'name' => 'login-button']) ?>
            </div>
            
            <div class="customer-links">
                <p style="color: #666; margin-bottom: 10px;">还没有账号？</p>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> 注册客户账号', ['signup'], ['class' => 'btn btn-default', 'style' => 'margin: 5px;']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-briefcase"></i> 注册员工账号', ['employee-signup'], ['class' => 'btn btn-default', 'style' => 'margin: 5px;']) ?>
            </div>
            
            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <p style="color: #999; font-size: 12px;">💼 内部员工请访问：<?= Html::a('管理后台登录', ['/backend/web/index.php?r=site/login'], ['style' => 'color: #764ba2;']) ?></p>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
