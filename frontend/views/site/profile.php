<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $customer \common\models\Customer */

$this->title = '个人信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-sm-5">
            <div class="panel panel-primary">
                <div class="panel-heading">账号概览</div>
                <div class="panel-body">
                    <p><strong>客户编号：</strong><?= Html::encode($customer->CustomerID) ?></p>
                    <p><strong>会员等级：</strong><?= Html::encode($customer->MemberLevel ?: '普通会员') ?></p>
                    <p class="text-muted">编号与会员等级不可自行修改，如需变更请联系管理员。</p>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">当前信息</div>
                <div class="panel-body">
                    <p><strong>用户名：</strong><?= Html::encode($customer->Name) ?></p>
                    <p><strong>性别：</strong><?= Html::encode($customer->Gender) ?></p>
                    <p><strong>联系方式：</strong><?= Html::encode($customer->Contact) ?></p>
                    <p><strong>地址：</strong><?= Html::encode($customer->Address) ?></p>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="panel panel-success">
                <div class="panel-heading">修改可编辑信息</div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>
                        <?= $form->field($customer, 'Name')->textInput(['maxlength' => true])->label('用户名') ?>
                        <?= $form->field($customer, 'Gender')->dropDownList([
                            '男' => '男',
                            '女' => '女',
                        ], ['prompt' => '选择性别'])->label('性别') ?>
                        <?= $form->field($customer, 'Contact')->textInput(['maxlength' => true])->label('联系方式') ?>
                        <?= $form->field($customer, 'Address')->textInput(['maxlength' => true])->label('地址') ?>
                        <div class="form-group">
                            <?= Html::submitButton('保存修改', ['class' => 'btn btn-success']) ?>
                            <?= Html::a('重置', ['profile'], ['class' => 'btn btn-default']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                    <p class="text-muted">提示：仅可修改用户名、性别、联系方式、地址；ID 与会员等级为只读。</p>
                </div>
            </div>
        </div>
    </div>

    <p>
        <?= Html::a('返回工作台', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('查看宠物信息', ['pets'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
