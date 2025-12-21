<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pet-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'PetID') ?>

    <?= $form->field($model, 'CustomerID') ?>

    <?= $form->field($model, 'PetName') ?>

    <?= $form->field($model, 'Gender') ?>

    <?= $form->field($model, 'AgeYears') ?>

    <?php // echo $form->field($model, 'AgeMonths') ?>

    <?php // echo $form->field($model, 'HealthStatus') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
