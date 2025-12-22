<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Dog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'PetID')->textInput()->label('宠物编号') ?>

    <?= $form->field($model, 'DogBreedType')->dropDownList([ '大型犬' => '大型犬', '中型犬' => '中型犬', '小型犬' => '小型犬', ], ['prompt' => '选择体型'])->label('体型') ?>

    <?= $form->field($model, 'TrainingLevel')->dropDownList([ '未训练' => '未训练', '基础训练' => '基础训练', '高级训练' => '高级训练', ], ['prompt' => '选择训练水平'])->label('训练水平') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
