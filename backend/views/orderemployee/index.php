<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderEmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单-员工';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增关联', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'OrderID',
            'EmployeeID',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','OrderID'=>$model->OrderID,'EmployeeID'=>$model->EmployeeID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','OrderID'=>$model->OrderID,'EmployeeID'=>$model->EmployeeID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','OrderID'=>$model->OrderID,'EmployeeID'=>$model->EmployeeID],[
                        'data'=>['confirm'=>'确认删除该关联？','method'=>'post']
                    ]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
