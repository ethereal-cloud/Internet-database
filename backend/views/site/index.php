<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '后台管理中心';
$user = Yii::$app->user->identity;
$role = $user->role ?? 'guest';
$isAdmin = $role === 'admin';
$isEmployee = $role === 'employee';
$employeeId = $isEmployee ? $user->getEmployeeId() : null;
$roleLabel = $isAdmin ? '管理员' : ($isEmployee ? '员工' : '访客');
$roleMode = $isAdmin ? '全权限' : ($isEmployee ? '只读 / 部分编辑' : '只读');
$roleEntry = $isAdmin ? '管理概览' : ($isEmployee ? '我的任务' : '访客入口');

$navItems = [
    ['label' => '宠物管理', 'url' => ['/pet/index']],
    ['label' => '员工管理', 'url' => ['/employee/index']],
    ['label' => '顾客管理', 'url' => ['/customer/index']],
    ['label' => '订单管理', 'url' => ['/fosterorder/index']],
    ['label' => '服务管理', 'url' => ['/fosterservice/index']],
];

$cards = [
    [
        'title' => '宠物管理',
        'subtitle' => $isAdmin ? '宠物档案与状态维护' : '仅查看匹配订单的宠物',
        'desc' => $isAdmin
            ? '集中管理宠物信息、健康状态与寄养记录，快速定位每只宠物的服务轨迹。'
            : '仅展示与你负责订单关联的宠物信息，无新增、删除或编辑权限。',
        'meta' => $isAdmin ? '权限：增删改查' : '权限：仅查看',
        'actions' => [
            ['label' => $isAdmin ? '进入管理' : '查看列表', 'url' => ['/pet/index'], 'class' => 'btn-primary'],
        ],
        'extra' => $isAdmin ? [
            ['label' => '新增猫', 'url' => ['/pet/create', 'type' => 'cat'], 'class' => 'btn-ghost'],
            ['label' => '新增狗', 'url' => ['/pet/create', 'type' => 'dog'], 'class' => 'btn-ghost'],
        ] : [],
        'accent' => 'accent-amber',
    ],
    [
        'title' => $isEmployee ? '个人信息' : '员工管理',
        'subtitle' => $isAdmin ? '员工档案与岗位维护' : '我的信息与联系方式',
        'desc' => $isAdmin
            ? '统一管理员工信息、岗位与联系方式，确保服务安排准确有序。新员工请通过前台注册。'
            : '查看个人完整资料并仅可更新联系方式。',
        'meta' => $isAdmin ? '权限：删改查（新增请注册）' : '权限：仅查看 / 修改联系方式',
        'actions' => $isAdmin ? [
            ['label' => '进入管理', 'url' => ['/employee/index'], 'class' => 'btn-primary'],
        ] : [
            ['label' => '我的信息', 'url' => ['/employee/view', 'id' => $employeeId], 'class' => 'btn-primary'],
            ['label' => '修改联系方式', 'url' => ['/employee/update', 'id' => $employeeId], 'class' => 'btn-ghost'],
        ],
        'extra' => [],
        'accent' => 'accent-rose',
    ],
    [
        'title' => '顾客管理',
        'subtitle' => $isAdmin ? '顾客档案与服务记录' : '仅查看匹配订单的顾客',
        'desc' => $isAdmin
            ? '查看顾客资料、服务历史与偏好，为精细化服务提供支撑。新顾客请通过前台注册。'
            : '仅可查看与你负责订单关联的顾客信息。',
        'meta' => $isAdmin ? '权限：删改查（新增请注册）' : '权限：仅查看',
        'actions' => [
            ['label' => $isAdmin ? '进入管理' : '查看列表', 'url' => ['/customer/index'], 'class' => 'btn-primary'],
        ],
        'extra' => [],
        'accent' => 'accent-sand',
    ],
    [
        'title' => '订单管理',
        'subtitle' => $isAdmin ? '寄养订单与进度跟踪' : '我的服务订单',
        'desc' => $isAdmin
            ? '创建与查看订单进度，订单信息不可修改以保证一致性。'
            : '仅查看与你服务号匹配的订单信息。',
        'meta' => $isAdmin ? '权限：增删查（不可修改）' : '权限：仅查看',
        'actions' => [
            ['label' => $isAdmin ? '查看订单' : '查看我的订单', 'url' => ['/fosterorder/index'], 'class' => 'btn-primary'],
        ],
        'extra' => $isAdmin ? [
            ['label' => '新增订单', 'url' => ['/fosterorder/create'], 'class' => 'btn-ghost'],
        ] : [],
        'accent' => 'accent-indigo',
    ],
    [
        'title' => '服务管理',
        'subtitle' => $isAdmin ? '服务项目与价格维护' : '查看全部服务项目',
        'desc' => $isAdmin
            ? '维护服务类型、价格与时长，支持订单精准匹配。'
            : '查看服务项目与价格信息。',
        'meta' => $isAdmin ? '权限：增删改查' : '权限：仅查看',
        'actions' => [
            ['label' => $isAdmin ? '进入管理' : '查看服务', 'url' => ['/fosterservice/index'], 'class' => 'btn-primary'],
        ],
        'extra' => [],
        'accent' => 'accent-olive',
    ],
];
?>
<div class="admin-home">
    <div class="admin-announcement">宠物寄养后台 · 专注每一只小生命的舒适照护</div>

    <header class="admin-header">
        <div class="brand-block">
            <div class="brand-mark">PawCare</div>
            <div class="brand-tagline">宠物寄养管理中心</div>
        </div>
        <div class="admin-actions">
            <div class="role-chip"><?= Html::encode($roleLabel) ?>专用</div>
        </div>
    </header>

    <section class="admin-hero">
        <div class="hero-copy">
            <p class="hero-kicker">Dogs & Cats Inspired</p>
            <h1>守护好每一个毛孩子的健康</h1>
            <p class="hero-text">请检查好自己的订单信息，给家长一个安心的交代。</p>
            <div class="hero-stats">
                <div>
                    <span>当前身份</span>
                    <strong><?= Html::encode($roleLabel) ?></strong>
                </div>
                <div>
                    <span>权限模式</span>
                    <strong><?= Html::encode($roleMode) ?></strong>
                </div>
                <div>
                    <span>推荐入口</span>
                    <strong><?= Html::a('进入订单', ['/fosterorder/index']) ?></strong>
                </div>
            </div>
        </div>
        <div class="hero-media">
            <img src="<?= Yii::getAlias('@web') ?>/images/pngtree-happy-small-dog-is-running-on-a-grass-in-park-in-image_15655854.jpg" alt="寄养犬只展示">
        </div>
        <div class="hero-panel">
            <div class="hero-panel-title">今日工作提示</div>
            <ul class="hero-list">
                <li>查看今日订单状态与到期服务</li>
                <li>核对顾客联系方式是否完整</li>
                <li>确认服务项目价格是否最新</li>
            </ul>
            <div class="hero-panel-footer">
                <?= Html::a('立即进入订单', ['/fosterorder/index'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('查看服务列表', ['/fosterservice/index'], ['class' => 'btn btn-ghost']) ?>
            </div>
        </div>
    </section>

    <section class="module-grid">
        <?php foreach ($cards as $card): ?>
            <article class="module-card <?= Html::encode($card['accent']) ?>">
                <div class="module-info">
                    <div class="module-header">
                        <h3><?= Html::encode($card['title']) ?></h3>
                        <span><?= Html::encode($card['subtitle']) ?></span>
                    </div>
                    <p class="module-desc"><?= Html::encode($card['desc']) ?></p>
                </div>
                <div class="module-meta"><?= Html::encode($card['meta']) ?></div>
                <div class="module-actions">
                    <?php foreach ($card['actions'] as $action): ?>
                        <?= Html::a(Html::encode($action['label']), $action['url'], ['class' => 'btn ' . $action['class']]) ?>
                    <?php endforeach; ?>
                    <?php foreach ($card['extra'] as $action): ?>
                        <?= Html::a(Html::encode($action['label']), $action['url'], ['class' => 'btn ' . $action['class']]) ?>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</div>
