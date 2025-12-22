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
        <?php 
        // 每次加载页面时清空宠物选择，强制用户重新选择
        $model->PetID = null;
        $form = ActiveForm::begin(); 
        ?>

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

        <div class="form-group" id="caregiver-group" style="display: none;">
            <label class="control-label">选择护理员 <span class="text-danger">*</span></label>
            <select name="caregiverId" id="caregiver-select" class="form-control" disabled>
                <option value="" disabled selected hidden>请先选择服务</option>
            </select>
            <p class="help-block" id="caregiver-hint" style="color: #999;">请先选择服务</p>
        </div>

        <div class="form-group" id="luxury-staff-group" style="display: none;">
            <label class="control-label">选择豪华服务职员 <span class="text-danger">*</span></label>
            <select name="luxuryStaffIds[]" id="luxury-staff-select" class="form-control" multiple size="5" disabled>
                <option value="" disabled selected hidden>请先选择护理员</option>
            </select>
            <p class="help-block" id="luxury-staff-hint" style="color: #999;">请先选择护理员</p>
        </div>

        <?= $form->field($model, 'StartTime')->input('datetime-local', [
            'value' => date('Y-m-d\TH:i'),
            'min' => date('Y-m-d\TH:i')
        ])->label('寄养开始时间') ?>

        <?= $form->field($model, 'EndTime')->input('datetime-local', [
            'value' => date('Y-m-d\TH:i', strtotime('+1 day')),
            'min' => date('Y-m-d\TH:i'),
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
    var caregiverGroup = document.getElementById('caregiver-group');
    var caregiverSelect = document.getElementById('caregiver-select');
    var caregiverHint = document.getElementById('caregiver-hint');
    var luxuryStaffGroup = document.getElementById('luxury-staff-group');
    var luxuryStaffSelect = document.getElementById('luxury-staff-select');
    var luxuryStaffHint = document.getElementById('luxury-staff-hint');
    var startTimeInput = document.querySelector('input[name="Fosterorder[StartTime]"]');
    var endTimeInput = document.querySelector('input[name="Fosterorder[EndTime]"]');
    var paymentAmountInput = document.getElementById('payment-amount');
    var timeErrorDiv = document.getElementById('time-error');
    var submitButton = document.querySelector('button[type="submit"]');
    
    // 员工数据：{employeeId: {name, position}}
    var employees = <?= json_encode(array_map(function($emp) {
        return [
            'name' => $emp->Name . ' (#' . $emp->EmployeeID . ')',
            'position' => $emp->Position,
        ];
    }, ArrayHelper::index(\common\models\Employee::find()->all(), 'EmployeeID'))) ?>;
    
    // 护理员列表
    var caregivers = {};
    var otherStaff = {};
    Object.keys(employees).forEach(function(empId) {
        if (employees[empId].position === '护理员') {
            caregivers[empId] = employees[empId].name;
        } else {
            otherStaff[empId] = employees[empId].name;
        }
    });
    
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
    
    // 服务数据：{serviceId: {category, price, label, serviceType}}
    var serviceData = <?= json_encode(array_map(function($service) {
        return [
            'category' => $service->PetCategory,
            'price' => $service->Price,
            'label' => $service->ServiceType . ' / ' . $service->PetCategory . ' (￥' . $service->Price . '/天)',
            'serviceType' => $service->ServiceType,
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
        // 如果开始时间晚于结束时间，自动更新结束时间为开始时间+1天
        var startTime = new Date(this.value);
        var endTime = new Date(endTimeInput.value);
        
        if (startTime >= endTime) {
            // 设置结束时间为开始时间后1天
            var newEndTime = new Date(startTime.getTime() + 24 * 60 * 60 * 1000);
            // 格式化为 datetime-local 格式
            var year = newEndTime.getFullYear();
            var month = String(newEndTime.getMonth() + 1).padStart(2, '0');
            var day = String(newEndTime.getDate()).padStart(2, '0');
            var hours = String(newEndTime.getHours()).padStart(2, '0');
            var minutes = String(newEndTime.getMinutes()).padStart(2, '0');
            endTimeInput.value = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
        }
        
        validateTime();
        calculateAmount();
    });
    endTimeInput.addEventListener('change', function() {
        validateTime();
        calculateAmount();
    });
    
    // 监听服务选择变化，启用/禁用员工选择
    serviceSelect.addEventListener('change', function() {
        var serviceId = this.value;
        
        if (serviceId && serviceData[serviceId]) {
            var service = serviceData[serviceId];
            
            if (service.serviceType === '普通寄养') {
                // 普通服务：只显示护理员选择框
                caregiverGroup.style.display = 'block';
                luxuryStaffGroup.style.display = 'none';
                caregiverSelect.disabled = false;
                caregiverSelect.innerHTML = '';
                
                // 添加占位选项
                var placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = '请选择护理员';
                placeholderOption.disabled = true;
                placeholderOption.selected = true;
                placeholderOption.hidden = true;
                caregiverSelect.appendChild(placeholderOption);
                
                // 添加护理员选项
                Object.keys(caregivers).forEach(function(empId) {
                    var option = document.createElement('option');
                    option.value = empId;
                    option.textContent = caregivers[empId];
                    caregiverSelect.appendChild(option);
                });
                
                caregiverHint.textContent = '普通服务只能选择1个护理员';
                caregiverHint.style.color = '#999';
                
            } else if (service.serviceType === '豪华寄养') {
                // 豪华服务：显示两个选择框
                caregiverGroup.style.display = 'block';
                luxuryStaffGroup.style.display = 'block';
                caregiverSelect.disabled = false;
                caregiverSelect.innerHTML = '';
                luxuryStaffSelect.disabled = true;
                luxuryStaffSelect.innerHTML = '<option value="" disabled selected hidden>请先选择护理员</option>';
                
                // 添加占位选项
                var placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = '请选择护理员';
                placeholderOption.disabled = true;
                placeholderOption.selected = true;
                placeholderOption.hidden = true;
                caregiverSelect.appendChild(placeholderOption);
                
                // 添加护理员选项
                Object.keys(caregivers).forEach(function(empId) {
                    var option = document.createElement('option');
                    option.value = empId;
                    option.textContent = caregivers[empId];
                    caregiverSelect.appendChild(option);
                });
                
                caregiverHint.textContent = '豪华服务必须选择1个护理员';
                caregiverHint.style.color = '#d9534f';
                luxuryStaffHint.textContent = '请先选择护理员';
                luxuryStaffHint.style.color = '#999';
            }
        } else {
            // 隐藏所有员工选择
            caregiverGroup.style.display = 'none';
            luxuryStaffGroup.style.display = 'none';
            caregiverSelect.disabled = true;
            luxuryStaffSelect.disabled = true;
        }
        
        calculateAmount();
    });
    
    // 监听护理员选择（豪华服务时启用其他职员选择）
    caregiverSelect.addEventListener('change', function() {
        var serviceId = serviceSelect.value;
        if (!serviceId || !serviceData[serviceId]) return;
        
        var service = serviceData[serviceId];
        
        if (service.serviceType === '豪华寄养' && this.value) {
            // 启用豪华服务职员选择
            luxuryStaffSelect.disabled = false;
            luxuryStaffSelect.innerHTML = '';
            
            // 添加占位选项（多选框无需hidden，因为用户可以看到占位文本）
            var placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = '请选择其他职位员工（Ctrl+点击多选）';
            placeholderOption.disabled = true;
            placeholderOption.selected = true;
            luxuryStaffSelect.appendChild(placeholderOption);
            
            // 添加其他职位员工选项
            Object.keys(otherStaff).forEach(function(empId) {
                var option = document.createElement('option');
                option.value = empId;
                option.textContent = otherStaff[empId];
                luxuryStaffSelect.appendChild(option);
            });
            
            luxuryStaffHint.textContent = '请选择2个其他职位员工（按住Ctrl键多选）';
            luxuryStaffHint.style.color = '#d9534f';
        }
    });
    
    // 监听豪华服务职员选择
    luxuryStaffSelect.addEventListener('change', function() {
        // 过滤掉占位选项
        var selectedCount = Array.from(this.selectedOptions).filter(function(opt) {
            return opt.value !== '';
        }).length;
        
        if (selectedCount !== 2) {
            luxuryStaffHint.textContent = '已选' + selectedCount + '个员工，必须选择2个！';
            luxuryStaffHint.style.color = '#d9534f';
            submitButton.disabled = true;
        } else {
            luxuryStaffHint.textContent = '✅ 已正确选择2个员工';
            luxuryStaffHint.style.color = '#5cb85c';
            submitButton.disabled = false;
        }
    });
    
    serviceSelect.addEventListener('change', calculateAmount);
    // 表单提交验证
    document.querySelector('form').addEventListener('submit', function(e) {
        var serviceId = serviceSelect.value;
        if (!serviceId) {
            alert('请选择服务！');
            e.preventDefault();
            return false;
        }
        
        var service = serviceData[serviceId];
        var caregiverId = caregiverSelect.value;
        
        if (!caregiverId) {
            alert('请选择护理员！');
            e.preventDefault();
            return false;
        }
        
        if (service.serviceType === '豪华寄养') {
            var luxuryStaff = Array.from(luxuryStaffSelect.selectedOptions);
            if (luxuryStaff.length !== 2) {
                alert('豪华服务必须选择2个其他职位员工！');
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
});
</script>
