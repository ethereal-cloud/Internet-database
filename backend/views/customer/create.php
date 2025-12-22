<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = '新增客户';
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
