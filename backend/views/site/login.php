<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '后台登录';
$baseUrl = Yii::$app->request->baseUrl;
$frontendPath = str_replace('/backend/web', '/frontend/web/index.php', $baseUrl);
if ($frontendPath === $baseUrl) {
    $frontendPath = '/frontend/web/index.php';
}
$frontendLoginUrl = Yii::$app->request->hostInfo . $frontendPath . '?r=site/login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-page">
    <div class="login-card">
        <div class="login-icon">
            <i class="glyphicon glyphicon-lock"></i>
        </div>
        <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
        <p class="login-subtitle">管理员 / 员工专用入口</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'login-form']]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => '用户名'])->label('账号') ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码'])->label('密码') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('记住我') ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> 登录管理后台', ['class' => 'btn login-button', 'name' => 'login-button']) ?>
            </div>
            
            <div class="login-footnote">
                <p>仅限管理员与员工登录</p>
                <p>客户请访问：<?= Html::a('客户端登录', $frontendLoginUrl, ['class' => 'login-link']) ?></p>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
