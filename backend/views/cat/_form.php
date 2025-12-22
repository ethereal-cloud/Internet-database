<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Cat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'PetID')->textInput()->label('宠物编号') ?>

    <?= $form->field($model, 'FurLength')->dropDownList([ '短毛' => '短毛', '中毛' => '中毛', '长毛' => '长毛', ], ['prompt' => '选择毛长'])->label('毛长') ?>

    <?= $form->field($model, 'Personality')->textInput(['maxlength' => true])->label('性格') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
