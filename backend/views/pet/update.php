<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Pet */
/* @var $cat common\models\Cat|null */
/* @var $dog common\models\Dog|null */
/* @var $type string */

$this->title = '编辑宠物：' . $model->PetID;
$this->params['breadcrumbs'][] = ['label' => '宠物列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PetID, 'url' => ['view', 'id' => $model->PetID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="pet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cat' => $cat,
        'dog' => $dog,
        'type' => $type,
    ]) ?>

</div>
