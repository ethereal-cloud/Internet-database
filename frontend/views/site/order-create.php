<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */
/* @var $customer common\models\Customer */
/* @var $pets common\models\Pet[] */
/* @var $services common\models\Fosterservice[] */

$this->title = '创建订单';
$this->params['breadcrumbs'][] = ['label' => '订单信息', 'url' => ['orders']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">客户：<?= Html::encode($customer->Name ?: $customer->CustomerID) ?></p>

    <div class="order-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'PetID')->dropDownList(
            ArrayHelper::merge(
                ['' => '-- 请选择宠物 --'],
                ArrayHelper::map($pets, 'PetID', function($pet) {
                    $type = $pet->cat ? '猫' : ($pet->dog ? '狗' : '');
                    return $pet->PetName . ' (' . $type . ')';
                })
            ),
            [
                //  value="" 的占位符，用来不可选择提示字符。
                'options' => [
                    '' => ['disabled' => true, 'selected' => true, 'hidden' => true]
                ]
            ]
        )->label('选择宠物') ?>

        <?= $form->field($model, 'ServiceID')->dropDownList(
            ['' => '请先选择宠物'],
            [
                'id' => 'service-select',
                'disabled' => true,
                'options' => [
                    '' => ['disabled' => true, 'selected' => true, 'hidden' => true]
                ]
            ]
        )->label('选择服务') ?>

        <?= $form->field($model, 'StartTime')->input('datetime-local', [
            'value' => date('Y-m-d\TH:i')
        ])->label('寄养开始时间') ?>

        <?= $form->field($model, 'EndTime')->input('datetime-local', [
            'value' => date('Y-m-d\TH:i', strtotime('+1 day')),
            'id' => 'end-time-input'
        ])->label('寄养结束时间') ?>
        <div id="time-error" class="alert alert-danger" style="display: none; margin-top: -10px; margin-bottom: 15px;">
            <i class="glyphicon glyphicon-exclamation-sign"></i> 结束时间必须晚于开始时间！
        </div>

        <?= $form->field($model, 'PaymentAmount')->textInput([
            'type' => 'number', 
            'step' => '0.01',
            'min' => '0',
            'id' => 'payment-amount',
            'readonly' => true,
            'style' => 'background-color: #f5f5f5;'
        ])->label('支付金额（元）') ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> 创建订单', ['class' => 'btn btn-success']) ?>
            <?= Html::a('取消', ['orders'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
// 根据服务和时间自动计算金额
document.addEventListener('DOMContentLoaded', function() {
    var petSelect = document.querySelector('select[name="Fosterorder[PetID]"]');
    var serviceSelect = document.getElementById('service-select');
    var startTimeInput = document.querySelector('input[name="Fosterorder[StartTime]"]');
    var endTimeInput = document.querySelector('input[name="Fosterorder[EndTime]"]');
    var paymentAmountInput = document.getElementById('payment-amount');
    var timeErrorDiv = document.getElementById('time-error');
    var submitButton = document.querySelector('button[type="submit"]');
    
    // 验证时间的函数
    function validateTime() {
        var startTime = startTimeInput.value;
        var endTime = endTimeInput.value;
        
        if (startTime && endTime) {
            var start = new Date(startTime);
            var end = new Date(endTime);
            
            if (end <= start) {
                // 显示错误提示
                timeErrorDiv.style.display = 'block';
                submitButton.disabled = true;
                paymentAmountInput.value = '';
                return false;
            } else {
                // 隐藏错误提示
                timeErrorDiv.style.display = 'none';
                submitButton.disabled = false;
                return true;
            }
        }
        return true;
    }
    
    // 宠物类型映射：petId => petCategory
    var petCategories = <?= json_encode(ArrayHelper::map($pets, 'PetID', function($pet) {
        if ($pet->cat) {
            return '猫';
        } elseif ($pet->dog) {
            return $pet->dog->DogBreedType; // '大型犬', '中型犬', '小型犬'
        }
        return '';
    })) ?>;
    
    // 服务数据：{serviceId: {category, price, label}}
    var serviceData = <?= json_encode(array_map(function($service) {
        return [
            'category' => $service->PetCategory,
            'price' => $service->Price,
            'label' => $service->ServiceType . ' / ' . $service->PetCategory . ' (￥' . $service->Price . '/天)',
        ];
    }, ArrayHelper::index($services, 'ServiceID'))) ?>;
    
    // 当选择宠物时，筛选服务列表
    petSelect.addEventListener('change', function() {
        var petId = this.value;
        var petCategory = petCategories[petId];
        
        // 清空并重置服务下拉框
        serviceSelect.innerHTML = '';
        
        // 添加占位符 option（不可选择、隐藏）
        var placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        
        if (petCategory) {
            // 启用服务选择框
            serviceSelect.disabled = false;
            placeholderOption.textContent = '-- 请选择服务 --';
            placeholderOption.disabled = true;
            placeholderOption.selected = true;
            placeholderOption.hidden = true;
            serviceSelect.appendChild(placeholderOption);
            
            // 根据宠物类型筛选服务
            Object.keys(serviceData).forEach(function(serviceId) {
                var service = serviceData[serviceId];
                
                // 匹配逻辑：猫匹配猫，狗匹配对应的狗类型
                if (service.category === petCategory) {
                    var option = document.createElement('option');
                    option.value = serviceId;
                    option.textContent = service.label;
                    serviceSelect.appendChild(option);
                }
            });
        } else {
            // 禁用服务选择框
            serviceSelect.disabled = true;
            placeholderOption.textContent = '请先选择宠物';
            placeholderOption.disabled = true;
            placeholderOption.selected = true;
            serviceSelect.appendChild(placeholderOption);
        }
        
        // 清空金额
        paymentAmountInput.value = '';
    });
    
    function calculateAmount() {
        var serviceId = serviceSelect.value;
        var startTime = startTimeInput.value;
        var endTime = endTimeInput.value;
        
        // 先验证时间
        if (!validateTime()) {
            return;
        }
        
        if (serviceId && startTime && endTime) {
            var start = new Date(startTime);
            var end = new Date(endTime);
            var days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            
            if (days > 0) {
                var pricePerDay = serviceData[serviceId]?.price || 0;
                var totalAmount = days * pricePerDay;
                paymentAmountInput.value = totalAmount.toFixed(2);
            } else {
                paymentAmountInput.value = '';
            }
        }
    }
    
    // 监听时间输入变化
    startTimeInput.addEventListener('change', function() {
        validateTime();
        calculateAmount();
    });
    endTimeInput.addEventListener('change', function() {
        validateTime();
        calculateAmount();
    });
    
    serviceSelect.addEventListener('change', calculateAmount);
});
</script>
