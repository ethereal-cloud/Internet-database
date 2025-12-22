<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Pet */
/* @var $cat common\models\Cat|null */
/* @var $dog common\models\Dog|null */
/* @var $type string */
?>

<div class="pet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'PetID')->textInput(['readonly' => true])->label('宠物编号') ?>
    <?php endif; ?>

    <?= $form->field($model, 'CustomerID')->textInput()->label('客户编号') ?>

    <?= $form->field($model, 'PetName')->textInput(['maxlength' => true])->label('宠物名称') ?>

    <?= $form->field($model, 'Gender')->dropDownList([ '公' => '公', '母' => '母', ], ['prompt' => '选择性别'])->label('性别') ?>

    <?= $form->field($model, 'AgeYears')->textInput(['type' => 'number', 'min' => 0])->label('年龄（岁）') ?>

    <?= $form->field($model, 'AgeMonths')->textInput(['type' => 'number', 'min' => 0, 'max' => 11])->label('年龄（月）') ?>

    <?= $form->field($model, 'HealthStatus')->textInput(['maxlength' => true])->label('健康状况') ?>

    <?php if ($type === 'cat' && $cat): ?>
        <h4>猫信息</h4>
        <?= $form->field($cat, 'FurLength')->dropDownList([
            '短毛' => '短毛',
            '中毛' => '中毛',
            '长毛' => '长毛',
        ], ['prompt' => '选择毛长']) ?>
        <?= $form->field($cat, 'Personality')->textInput(['maxlength' => true]) ?>
    <?php elseif ($type === 'dog' && $dog): ?>
        <h4>狗信息</h4>
        <?= $form->field($dog, 'DogBreedType')->dropDownList([
            '小型犬' => '小型犬',
            '中型犬' => '中型犬',
            '大型犬' => '大型犬',
        ], ['prompt' => '选择体型']) ?>
        <?= $form->field($dog, 'TrainingLevel')->dropDownList([
            '未训练' => '未训练',
            '基础训练' => '基础训练',
            '高级训练' => '高级训练',
        ], ['prompt' => '选择训练水平']) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
