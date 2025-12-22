<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */

$this->title = '服务详情：' . $model->ServiceID;
$this->params['breadcrumbs'][] = ['label' => '服务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fosterservice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ServiceID',
            'ServiceType',
            'PetCategory',
            'Price',
            'Duration',
        ],
    ]) ?>

</div>
