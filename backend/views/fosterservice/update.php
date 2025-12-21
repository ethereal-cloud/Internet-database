<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */

$this->title = 'Update Fosterservice: ' . $model->ServiceID;
$this->params['breadcrumbs'][] = ['label' => 'Fosterservices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ServiceID, 'url' => ['view', 'id' => $model->ServiceID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fosterservice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
