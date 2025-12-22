<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */

$this->title = '编辑服务：' . $model->ServiceID;
$this->params['breadcrumbs'][] = ['label' => '服务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ServiceID, 'url' => ['view', 'id' => $model->ServiceID]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="fosterservice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
