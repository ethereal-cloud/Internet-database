<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FosterserviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '服务列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterservice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ServiceID',
            'ServiceType',
            'PetCategory',
            'Price',
            'Duration',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
