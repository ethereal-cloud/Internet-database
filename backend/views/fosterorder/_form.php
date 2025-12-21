<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterorder-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'OrderID')->textInput() ?>

    <?= $form->field($model, 'CustomerID')->textInput() ?>

    <?= $form->field($model, 'PetID')->textInput() ?>

    <?= $form->field($model, 'ServiceID')->textInput() ?>

    <?= $form->field($model, 'StartTime')->textInput() ?>

    <?= $form->field($model, 'EndTime')->textInput() ?>

    <?= $form->field($model, 'OrderStatus')->dropDownList([ '未支付' => '未支付', '已支付' => '已支付', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'PaymentAmount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
