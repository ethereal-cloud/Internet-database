<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterservice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ServiceID')->textInput()->label('服务编号') ?>

    <?= $form->field($model, 'ServiceType')->dropDownList([ '普通寄养' => '普通寄养', '豪华寄养' => '豪华寄养', ], ['prompt' => '选择服务类型'])->label('服务类型') ?>

    <?= $form->field($model, 'PetCategory')->dropDownList([ '猫' => '猫', '小型犬' => '小型犬', '中型犬' => '中型犬', '大型犬' => '大型犬', ], ['prompt' => '选择适用宠物'])->label('适用宠物') ?>

    <?= $form->field($model, 'Price')->textInput(['maxlength' => true])->label('价格') ?>

    <?= $form->field($model, 'Duration')->textInput()->label('持续天数') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
