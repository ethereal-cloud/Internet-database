<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cat */

$this->title = '新增猫';
$this->params['breadcrumbs'][] = ['label' => '猫信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
