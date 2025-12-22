<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->context->layout = 'login';
$this->title = '客户登录';
$baseUrl = Yii::$app->request->baseUrl;
$backendPath = str_replace('/frontend/web', '/backend/web/index.php', $baseUrl);
if ($backendPath === $baseUrl) {
    $backendPath = '/backend/web/index.php';
}
$backendLoginUrl = Yii::$app->request->hostInfo . $backendPath . '?r=site/login';
$this->params['backendLoginUrl'] = $backendLoginUrl;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-page">
    <div class="login-card">
        <div class="login-icon">
            <i class="glyphicon glyphicon-user"></i>
        </div>
        <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
        <p class="login-subtitle">欢迎来到宠物寄养平台</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'login-form']]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '用户名'])->label('账号') ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码'])->label('密码') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('记住我') ?>

            <div class="login-helper">
                忘记密码？<?= Html::a('重置密码', ['site/request-password-reset'], ['class' => 'login-link']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> 登录', ['class' => 'btn login-button', 'name' => 'login-button']) ?>
            </div>
            
            <div class="login-actions">
                <p class="login-actions-title">还没有账号？</p>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> 注册客户账号', ['signup'], ['class' => 'btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-briefcase"></i> 注册员工账号', ['employee-signup'], ['class' => 'btn']) ?>
            </div>
            
            <div class="login-footnote">
                <p>内部员工请访问：<?= Html::a('管理后台登录', $backendLoginUrl, ['class' => 'login-link']) ?></p>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
