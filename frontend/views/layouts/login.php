<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-layout">
<?php $this->beginBody() ?>

<div class="wrap">
    <header class="admin-shell">
        <div class="top-strip">宠物寄养前台 · 欢迎登录</div>
        <div class="nav-row">
            <?= Html::a(
                'Ruff &amp; Fetch <span>客户服务中心</span>',
                ['/site/index'],
                ['class' => 'brand-link', 'encode' => false]
            ) ?>
            <nav class="nav-menu">
                <?= Html::a('登录', ['/site/login']) ?>
                <?= Html::a('注册', ['/site/signup']) ?>
            </nav>
            <div class="nav-actions">
                <span class="role-badge">访客</span>
                <?= Html::a('管理后台', $this->params['backendLoginUrl'] ?? ['/backend/web/index.php?r=site/login'], ['class' => 'nav-button ghost']) ?>
            </div>
        </div>
    </header>

    <div class="container">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
