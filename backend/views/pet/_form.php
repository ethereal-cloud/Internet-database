<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Pet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'PetID')->textInput() ?>

    <?= $form->field($model, 'CustomerID')->textInput() ?>

    <?= $form->field($model, 'PetName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Gender')->dropDownList([ '公' => '公', '母' => '母', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'AgeYears')->textInput() ?>

    <?= $form->field($model, 'AgeMonths')->textInput() ?>

    <?= $form->field($model, 'HealthStatus')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
