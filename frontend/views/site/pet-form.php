<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $pet \common\models\Pet */
/* @var $cat \common\models\Cat|null */
/* @var $dog \common\models\Dog|null */
/* @var $type string */

$isNew = $pet->isNewRecord;
$this->title = $isNew ? '新增宠物' : '编辑宠物';
$this->params['breadcrumbs'][] = ['label' => '宠物信息', 'url' => ['pets']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-pet-form">
    <h1><?= Html::encode($this->title) ?> <small><?= $type === 'cat' ? '（猫）' : '（狗）' ?></small></h1>
    <p class="text-muted">仅可创建/修改自己名下的宠物。主键和客户ID不可修改。</p>

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($pet, 'PetName')->textInput(['maxlength' => true])->label('宠物名称') ?>
        <?= $form->field($pet, 'Gender')->dropDownList(['公' => '公', '母' => '母'], ['prompt' => '选择性别'])->label('性别') ?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($pet, 'AgeYears')->textInput(['type' => 'number', 'min' => 0])->label('年龄（岁）') ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($pet, 'AgeMonths')->textInput(['type' => 'number', 'min' => 0, 'max' => 11])->label('年龄（月）') ?>
            </div>
        </div>
        <?= $form->field($pet, 'HealthStatus')->textInput(['maxlength' => true])->label('健康状况') ?>

        <?php if ($type === 'cat' && $cat): ?>
            <h4>猫信息</h4>
            <?= $form->field($cat, 'FurLength')->dropDownList([
                '短毛' => '短毛',
                '中毛' => '中毛',
                '长毛' => '长毛',
            ], ['prompt' => '选择毛长'])->label('毛长') ?>
            <?= $form->field($cat, 'Personality')->textInput(['maxlength' => true])->label('性格') ?>
        <?php elseif ($type === 'dog' && $dog): ?>
            <h4>狗信息</h4>
            <?= $form->field($dog, 'DogBreedType')->dropDownList([
                '小型犬' => '小型犬',
                '中型犬' => '中型犬',
                '大型犬' => '大型犬',
            ], ['prompt' => '选择体型'])->label('体型') ?>
            <?= $form->field($dog, 'TrainingLevel')->dropDownList([
                '未训练' => '未训练',
                '基础训练' => '基础训练',
                '高级训练' => '高级训练',
            ], ['prompt' => '选择训练水平'])->label('训练水平') ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton($isNew ? '创建' : '保存修改', ['class' => 'btn btn-success']) ?>
            <?= Html::a('返回列表', ['pets'], ['class' => 'btn btn-default']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
