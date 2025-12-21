<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'EmployeeID') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Gender') ?>

    <?= $form->field($model, 'Position') ?>

    <?php // echo $form->field($model, 'Contact') ?>

    <?php // echo $form->field($model, 'HireDate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
