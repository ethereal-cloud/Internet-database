<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FosterserviceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fosterservice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ServiceID') ?>

    <?= $form->field($model, 'ServiceType') ?>

    <?= $form->field($model, 'PetCategory') ?>

    <?= $form->field($model, 'Price') ?>

    <?= $form->field($model, 'Duration') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
