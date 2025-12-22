<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $customer \common\models\Customer */
/* @var $orders \common\models\Fosterorder[] */

$this->title = '订单信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-orders">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">客户：<?= Html::encode($customer->Name ?: $customer->CustomerID) ?></p>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>订单ID</th>
                    <th>宠物</th>
                    <th>服务</th>
                    <th>起止时间</th>
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

    <p>
        <?= Html::a('返回工作台', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('查看寄养服务', ['services'], ['class' => 'btn btn-warning']) ?>
    </p>
</div>
