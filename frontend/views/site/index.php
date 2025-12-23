<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $customer \common\models\Customer|null */
/* @var $pets \common\models\Pet[]|null */
/* @var $petsCount int */
/* @var $orders \common\models\Fosterorder[]|null */
/* @var $ordersCount int */
/* @var $services \common\models\Fosterservice[]|null */
/* @var $servicesCount int */

$isCustomer = !Yii::$app->user->isGuest && (Yii::$app->user->identity->role ?? null) === 'customer';
$this->title = $isCustomer ? '宠物寄养工作台' : 'My Yii Application';
?>
<div class="site-index">
    <section class="home-hero">
        <div class="home-hero-title">
            <h1>把它交给我们，你放心忙你的</h1>
            <p>在Dogs&amp;Cats寄养，不只是“有人喂”，更是像在家一样被照顾。</p>
        </div>
        <div class="row home-hero-content">
            <div class="col-md-7 col-sm-6">
                <div class="home-hero-card">
                    <p class="home-hero-card-lead">为什么选择Dogs&amp;Cats寄养？</p>
                    <p>
                        我们提供干净通风的独立寄养空间、定时喂养与换水、每日清洁消毒与互动陪伴。 </p>
                         <p>寄养期间可随时视频/照片反馈，让你随时知道TA吃得好不好、精神好不好、有没有想你。 </p>
                         <p>无论是短途出差、旅行还是临时有事，我们都认真对待每一次托付。 </p>
                        <p> 每位毛孩子都会建立独立档案，记录饮食习惯、作息偏好与健康状态，确保照护细节不遗漏。</p>
                        <p>入住前后严格消毒、分区管理，减少交叉接触风险，让TA住得安心、你也更放心。</p>
                       <p> 我们还提供每日互动陪伴与轻度活动，让TA在寄养期间保持好心情与活力。
                    </p>
                </div>
            </div>
            <div class="col-md-5 col-sm-6">
                <?= Html::img('@web/images/pexels-photo-2253275.webp', [
                    'class' => 'home-hero-image img-responsive',
                    'alt' => 'Dogs & Cats 寄养',
                ]) ?>
            </div>
        </div>
    </section>
    <?php if ($isCustomer && isset($customer)): ?>
        <div class="customer-strip-tag">请选择功能页面</div>
        <div class="customer-strip">
            <div class="customer-strip-inner">
                <div class="customer-strip-left customer-strip-image">
                    <?= Html::img('@web/images/images.jpg', [
                        'alt' => '功能入口展示',
                    ]) ?>
                </div>
                <div class="customer-strip-right">
                    <div class="customer-strip-heading">
                        <h1>欢迎回来，<?= Html::encode($customer->Name ?: Yii::$app->user->identity->username) ?></h1>
                        <p class="lead">请选择要进入的功能页面</p>
                    </div>
                    <div class="customer-strip-menu">
                        <?= Html::a('个人信息', ['profile'], ['class' => 'customer-strip-link']) ?>
                        <?= Html::a('宠物信息', ['pets'], ['class' => 'customer-strip-link']) ?>
                        <?= Html::a('订单信息', ['orders'], ['class' => 'customer-strip-link']) ?>
                        <?= Html::a('寄养服务', ['services'], ['class' => 'customer-strip-link']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="body-content">
            <div class="customer-summary-block">
                <div class="row customer-summary-grid">
                <div class="col-sm-3">
                    <div class="panel panel-primary">
                        <div class="panel-heading">会员等级</div>
                        <div class="panel-body">
                            <h3><?= Html::encode($customer->MemberLevel ?: '普通会员') ?></h3>
                            <p class="text-muted">可在个人信息页更新联系方式/地址。</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-success">
                        <div class="panel-heading">我的宠物</div>
                        <div class="panel-body">
                            <h3><?= isset($petsCount) ? $petsCount : 0 ?> 只</h3>
                            <p class="text-muted">查看或补充宠物信息。</p>
                        </div>
                        <div class="panel-footer"><?= Html::a('前往宠物信息', ['pets']) ?></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">我的订单</div>
                        <div class="panel-body">
                            <h3><?= isset($ordersCount) ? $ordersCount : 0 ?> 单</h3>
                            <p class="text-muted">查看寄养记录与支付金额。</p>
                        </div>
                        <div class="panel-footer"><?= Html::a('前往订单信息', ['orders']) ?></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-warning">
                        <div class="panel-heading">寄养服务</div>
                        <div class="panel-body">
                            <h3><?= isset($servicesCount) ? $servicesCount : 0 ?> 项</h3>
                            <p class="text-muted">查看可选套餐与价格。</p>
                        </div>
                        <div class="panel-footer"><?= Html::a('查看服务列表', ['services']) ?></div>
                    </div>
                </div>
                </div>
            </div>

            <?php if (!empty($pets)): ?>
                <h3>我的宠物（最近）</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>名字</th>
                                <th>类别</th>
                                <th>性别</th>
                                <th>年龄</th>
                                <th>健康状况</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pets as $pet): ?>
                                <?php
                                    $type = $pet->cat ? '猫' : ($pet->dog ? '狗' : '未知');
                                    $extra = '';
                                    if ($pet->cat) {
                                        $extra = $pet->cat->FurLength . ' / ' . $pet->cat->Personality;
                                    } elseif ($pet->dog) {
                                        $extra = $pet->dog->DogBreedType . ' / ' . $pet->dog->TrainingLevel;
                                    }
                                ?>
                                <tr>
                                    <td><?= Html::encode($pet->PetID) ?></td>
                                    <td><?= Html::encode($pet->PetName) ?></td>
                                    <td><?= Html::encode($type) ?><?= $extra ? '（' . Html::encode($extra) . '）' : '' ?></td>
                                    <td><?= Html::encode($pet->Gender) ?></td>
                                    <td><?= Html::encode($pet->AgeYears . '岁' . $pet->AgeMonths . '月') ?></td>
                                    <td><?= Html::encode($pet->HealthStatus) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($orders)): ?>
                <h3>我的订单（最近）</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>订单ID</th>
                                <th>宠物</th>
                                <th>服务</th>
                                <th>时间</th>
                                <th>负责员工</th>
                                <th>状态</th>
                                <th>金额</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= Html::encode($order->OrderID) ?></td>
                                    <td><?= Html::encode($order->pet->PetName ?? $order->PetID) ?></td>
                                    <td><?= Html::encode(($order->service->ServiceType ?? '') . ' / ' . ($order->service->PetCategory ?? '')) ?></td>
                                    <td><?= Html::encode($order->StartTime . ' - ' . $order->EndTime) ?></td>
                                    <td>
                                        <?php
                                            $names = array_map(function($emp) {
                                                $contact = $emp->Contact ? '（' . $emp->Contact . '）' : '';
                                                return $emp->Name . $contact;
                                            }, $order->employees ?? []);
                                            echo $names ? Html::encode(implode('、', $names)) : '-';
                                        ?>
                                    </td>
                                    <td><?= Html::encode($order->OrderStatus) ?></td>
                                    <td><?= Html::encode($order->PaymentAmount) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($services)): ?>
                <h3>寄养服务（部分）</h3>
                <div class="row">
                    <?php foreach ($services as $service): ?>
                        <div class="col-sm-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?= Html::encode($service->ServiceType) ?> · <?= Html::encode($service->PetCategory) ?>
                                </div>
                                <div class="panel-body">
                                    <p class="lead">¥<?= Html::encode($service->Price) ?>/<?= Html::encode($service->Duration) ?>天</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="jumbotron">
            <h1>欢迎来到宠物寄养平台</h1>
            <p class="lead">请登录后进入客户工作台。</p>
            <p>
                <?= Html::a('登录', ['login'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('注册', ['signup'], ['class' => 'btn btn-success btn-lg']) ?>
            </p>
        </div>
    <?php endif; ?>
</div>
