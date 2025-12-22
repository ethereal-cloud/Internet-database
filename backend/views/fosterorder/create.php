<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */

$this->title = '新增订单';
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterorder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
