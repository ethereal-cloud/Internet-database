<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterservice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ServiceID')->textInput() ?>

    <?= $form->field($model, 'ServiceType')->dropDownList([ '普通寄养' => '普通寄养', '豪华寄养' => '豪华寄养', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'PetCategory')->dropDownList([ '猫' => '猫', '小型犬' => '小型犬', '中型犬' => '中型犬', '大型犬' => '大型犬', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'Price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Duration')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
