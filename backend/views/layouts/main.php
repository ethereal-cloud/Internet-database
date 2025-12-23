<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

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
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $isGuest = Yii::$app->user->isGuest;
    $user = $isGuest ? null : Yii::$app->user->identity;
    $role = $user->role ?? 'guest';
    $roleLabel = $role === 'admin' ? '管理员' : ($role === 'employee' ? '员工' : '访客');
    $navItems = [
        ['label' => '后台首页', 'url' => ['/site/index']],
        ['label' => '宠物管理', 'url' => ['/pet/index']],
        ['label' => $role === 'employee' ? '个人信息' : '员工管理', 'url' => ['/employee/index']],
        ['label' => '顾客管理', 'url' => ['/customer/index']],
        ['label' => '订单管理', 'url' => ['/fosterorder/index']],
        ['label' => '服务管理', 'url' => ['/fosterservice/index']],
    ];
    ?>
    <header class="admin-shell">
        <div class="top-strip">宠物寄养后台 · 今日进度一览</div>
        <div class="nav-row">
            <?= Html::a(
                'Dogs&amp;Cats <span>后台管理中心</span>',
                ['/site/index'],
                ['class' => 'brand-link', 'encode' => false]
            ) ?>
            <nav class="nav-menu">
                <?php if (!$isGuest): ?>
                    <?php foreach ($navItems as $item): ?>
                        <?= Html::a(Html::encode($item['label']), $item['url']) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= Html::a('登录', ['/site/login']) ?>
                <?php endif; ?>
            </nav>
            <div class="nav-actions">
                <span class="role-badge"><?= Html::encode($roleLabel) ?></span>
                <?php if ($isGuest): ?>
                    <?= Html::a('登录后台', ['/site/login'], ['class' => 'nav-button']) ?>
                <?php else: ?>
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                        <?= Html::submitButton(
                            '退出 (' . Html::encode($user->username) . ')',
                            ['class' => 'nav-button ghost']
                        ) ?>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container admin-container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
