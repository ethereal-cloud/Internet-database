<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FosterorderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fosterorders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterorder-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Fosterorder', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'OrderID',
            'CustomerID',
            'PetID',
            'ServiceID',
            'StartTime',
            //'EndTime',
            //'OrderStatus',
            //'PaymentAmount',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
