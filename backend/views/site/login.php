<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '管理后台登录';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.backend-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -20px;
    padding: 20px;
}
.backend-login-box {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-width: 400px;
    width: 100%;
}
.backend-login-box h1 {
    color: #764ba2;
    text-align: center;
    margin-bottom: 10px;
    font-weight: bold;
}
.backend-login-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
    font-size: 14px;
}
.backend-login-icon {
    text-align: center;
    font-size: 60px;
    color: #764ba2;
    margin-bottom: 20px;
}
.btn-backend-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    width: 100%;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
}
.btn-backend-login:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}
</style>

<div class="backend-login">
    <div class="backend-login-box">
        <div class="backend-login-icon">
            <i class="glyphicon glyphicon-lock"></i>
        </div>
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="backend-login-subtitle">👥 管理员 / 员工专用入口</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '用户名'])->label('账号') ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码'])->label('密码') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('记住我') ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> 登录管理后台', ['class' => 'btn btn-primary btn-backend-login', 'name' => 'login-button']) ?>
            </div>
            
            <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
                <p>⚠️ 仅限管理员和员工登录</p>
                <p>客户请访问：<?= Html::a('客户端登录', Yii::$app->params['frontendBaseUrl'] . '/index.php?r=site/login', ['style' => 'color: #667eea;']) ?></p>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
