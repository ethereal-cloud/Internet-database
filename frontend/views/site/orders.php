<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $customer \common\models\Customer */
/* @var $orders \common\models\Fosterorder[] */

$this->title = '订单信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-orders">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">客户：<?= Html::encode($customer->Name ?: $customer->CustomerID) ?></p>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> 创建新订单', ['order-create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">暂无订单记录</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= Html::encode($order->OrderID) ?></td>
                            <td><?= Html::encode($order->pet->PetName ?? $order->PetID) ?></td>
                            <td><?= Html::encode(($order->service->ServiceType ?? '') . ' / ' . ($order->service->PetCategory ?? '')) ?></td>
                            <td><?= Html::encode($order->StartTime . ' ~ ' . $order->EndTime) ?></td>
                            <td>
                                <?php
                                    $names = array_map(function($emp) {
                                        $contact = $emp->Contact ? '（' . $emp->Contact . '）' : '';
                                        return $emp->Name . $contact;
                                    }, $order->employees ?? []);
                                    echo $names ? Html::encode(implode('、', $names)) : '<span class="text-muted">待分配</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($order->OrderStatus === '已支付'): ?>
                                    <span class="label label-success"><?= Html::encode($order->OrderStatus) ?></span>
                                <?php else: ?>
                                    <span class="label label-warning"><?= Html::encode($order->OrderStatus) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>￥<?= Html::encode(number_format($order->PaymentAmount, 2)) ?></td>
                            <td>
                                <?php if ($order->OrderStatus === '未支付'): ?>
                                    <button class="btn btn-primary btn-sm pay-btn" 
                                            data-order-id="<?= $order->OrderID ?>"
                                            data-amount="<?= $order->PaymentAmount ?>">
                                        <i class="glyphicon glyphicon-credit-card"></i> 支付
                                    </button>
                                <?php else: ?>
                                    <span class="text-success"><i class="glyphicon glyphicon-ok"></i> 已完成</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p>
        <?= Html::a('返回工作台', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('查看寄养服务', ['services'], ['class' => 'btn btn-warning']) ?>
    </p>
</div>

<!-- 支付弹窗 -->
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="glyphicon glyphicon-credit-card"></i> 订单支付</h4>
            </div>
            <div class="modal-body">
                <div class="payment-info">
                    <p><strong>订单号：</strong><span id="pay-order-id"></span></p>
                    <p><strong>支付金额：</strong><span class="text-danger" style="font-size: 24px;">￥<span id="pay-amount"></span></span></p>
                    <hr>
                    <div class="alert alert-info">
                        <i class="glyphicon glyphicon-info-sign"></i> 这是模拟支付，点击"确认支付"后订单状态将更新为"已支付"。
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success" id="confirm-pay-btn">
                    <i class="glyphicon glyphicon-ok"></i> 确认支付
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var currentOrderId = null;
    
    // 点击支付按钮
    document.querySelectorAll('.pay-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentOrderId = this.getAttribute('data-order-id');
            var amount = this.getAttribute('data-amount');
            
            document.getElementById('pay-order-id').textContent = currentOrderId;
            document.getElementById('pay-amount').textContent = parseFloat(amount).toFixed(2);
            
            $('#payment-modal').modal('show');
        });
    });
    
    // 确认支付
    document.getElementById('confirm-pay-btn').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i> 支付中...';
        
        // 发送Ajax请求
        fetch('<?= Url::to(['order-pay']) ?>&id=' + currentOrderId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                $('#payment-modal').modal('hide');
                location.reload(); // 刷新页面显示最新状态
            } else {
                alert('❌ ' + data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="glyphicon glyphicon-ok"></i> 确认支付';
            }
        })
        .catch(error => {
            alert('❌ 支付请求失败，请稍后重试。');
            btn.disabled = false;
            btn.innerHTML = '<i class="glyphicon glyphicon-ok"></i> 确认支付';
        });
    });
});
</script>

<style>
.glyphicon-refresh-animate {
    animation: spin 1s infinite linear;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

