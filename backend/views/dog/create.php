<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dog */

$this->title = '新增狗';
$this->params['breadcrumbs'][] = ['label' => '狗信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dog-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
