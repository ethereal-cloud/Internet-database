<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Cat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'PetID')->textInput() ?>

    <?= $form->field($model, 'FurLength')->dropDownList([ '短毛' => '短毛', '中毛' => '中毛', '长毛' => '长毛', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'Personality')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
