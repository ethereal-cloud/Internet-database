<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Pet */
/* @var $cat common\models\Cat|null */
/* @var $dog common\models\Dog|null */
/* @var $type string */

$this->title = $type === 'dog' ? '新增狗' : '新增猫';
$this->params['breadcrumbs'][] = ['label' => '宠物列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cat' => $cat,
        'dog' => $dog,
        'type' => $type,
    ]) ?>

</div>
