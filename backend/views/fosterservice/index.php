<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FosterserviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fosterservices';
$this->params['breadcrumbs'][] = $this->title;
$isAdmin = (Yii::$app->user->identity->role ?? null) === 'admin';
?>
<div class="fosterservice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isAdmin): ?>
        <p>
            <?= Html::a('Create Fosterservice', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            [
                'label' => '关联订单',
                'format' => 'raw',
                'value' => function ($model) {
                    if (empty($model->fosterorders)) {
                        return '<span class="text-muted">暂无关联订单</span>';
                    }
                    $lines = [];
                    foreach ($model->fosterorders as $order) {
                        $lines[] = '订单 #' . Html::encode($order->OrderID)
                            . ' / 客户 ' . Html::encode($order->CustomerID)
                            . ' / 宠物 ' . Html::encode($order->PetID)
                            . ' / ' . Html::encode($order->OrderStatus);
                    }
                    return implode('<br>', $lines);
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
