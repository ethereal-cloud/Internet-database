<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '客户列表';
$this->params['breadcrumbs'][] = $this->title;
$role = Yii::$app->user->identity->role ?? 'guest';
$isAdmin = $role === 'admin';
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isAdmin): ?>
        <p class="text-muted">
            <i class="glyphicon glyphicon-info-sign"></i> 请通过前台注册流程创建新客户账户
        </p>
    <?php endif; ?>

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

            $isAdmin ? [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function($url,$model,$key){ return Html::a('查看',['view','id'=>$model->CustomerID]); },
                    'update' => function($url,$model,$key){ return Html::a('编辑',['update','id'=>$model->CustomerID]); },
                    'delete' => function($url,$model,$key){ return Html::a('删除',['delete','id'=>$model->CustomerID],[
                        'data'=>['confirm'=>'确认删除该客户？','method'=>'post']
                    ]); },
                ],
            ] : [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url,$model,$key){ return Html::a('查看',['view','id'=>$model->CustomerID]); },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
