<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cat */

$this->title = '编辑猫：' . $model->PetID;
$this->params['breadcrumbs'][] = ['label' => '猫信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PetID, 'url' => ['view', 'id' => $model->PetID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="cat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
