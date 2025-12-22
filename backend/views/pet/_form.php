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
        <?= $form->field($model, 'PetID')->textInput(['readonly' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'CustomerID')->textInput() ?>

    <?= $form->field($model, 'PetName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Gender')->dropDownList([ '公' => '公', '母' => '母', ], ['prompt' => '选择性别']) ?>

    <?= $form->field($model, 'AgeYears')->textInput(['type' => 'number', 'min' => 0]) ?>

    <?= $form->field($model, 'AgeMonths')->textInput(['type' => 'number', 'min' => 0, 'max' => 11]) ?>

    <?= $form->field($model, 'HealthStatus')->textInput(['maxlength' => true]) ?>

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
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
