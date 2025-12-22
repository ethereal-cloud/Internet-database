<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '员工列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增员工', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'EmployeeID',
            'user_id',
            'Name',
            'Gender',
            'Position',
            //'Contact',
            //'HireDate',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->EmployeeID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','id'=>$model->EmployeeID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','id'=>$model->EmployeeID],[
                        'data'=>['confirm'=>'确认删除该员工？','method'=>'post']
                    ]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
