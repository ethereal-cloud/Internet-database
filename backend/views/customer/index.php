<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '客户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增客户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'CustomerID',
            'user_id',
            'Name',
            'Gender',
            'Contact',
            //'Address',
            //'MemberLevel',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->CustomerID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','id'=>$model->CustomerID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','id'=>$model->CustomerID],[
                        'data'=>['confirm'=>'确认删除该客户？','method'=>'post']
                    ]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
