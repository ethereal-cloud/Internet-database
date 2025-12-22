<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FosterorderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterorder-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增订单', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'OrderID',
            'CustomerID',
            [
                'label' => '宠物',
                'value' => function($model) {
                    return $model->pet->PetName ?? $model->PetID;
                }
            ],
            [
                'label' => '服务',
                'value' => function($model) {
                    if (!$model->service) return $model->ServiceID;
                    return $model->service->ServiceType . ' / ' . $model->service->PetCategory;
                }
            ],
            'StartTime',
            'EndTime',
            [
                'label' => '负责员工',
                'value' => function($model) {
                    $names = array_map(function($emp) {
                        $contact = $emp->Contact ? '（' . $emp->Contact . '）' : '';
                        return $emp->Name . $contact;
                    }, $model->employees ?? []);
                    return $names ? implode('、', $names) : '-';
                }
            ],
            'OrderStatus',
            'PaymentAmount',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->OrderID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','id'=>$model->OrderID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','id'=>$model->OrderID],[
                        'data'=>['confirm'=>'确认删除该订单？','method'=>'post']
                    ]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
