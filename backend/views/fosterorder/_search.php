<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FosterorderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterorder-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'OrderID') ?>

    <?= $form->field($model, 'CustomerID') ?>

    <?= $form->field($model, 'PetID') ?>

    <?= $form->field($model, 'ServiceID') ?>

    <?= $form->field($model, 'StartTime') ?>

    <?php // echo $form->field($model, 'EndTime') ?>

    <?php // echo $form->field($model, 'OrderStatus') ?>

    <?php // echo $form->field($model, 'PaymentAmount') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
