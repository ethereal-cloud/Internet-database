<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '员工列表';
$this->params['breadcrumbs'][] = $this->title;
$role = Yii::$app->user->identity->role ?? 'guest';
$isAdmin = $role === 'admin';
$isEmployee = $role === 'employee';
$self = $isEmployee ? Yii::$app->user->identity->employee : null;
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isAdmin): ?>
        <p class="text-muted">
            <i class="glyphicon glyphicon-info-sign"></i> 请通过前台注册流程（需要邀请码）创建新员工账户
        </p>
    <?php endif; ?>

    <?php if ($isEmployee && $self): ?>
        <div class="panel panel-default">
            <div class="panel-heading">我的信息</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p><strong>员工编号：</strong><?= Html::encode($self->EmployeeID) ?></p>
                        <p><strong>姓名：</strong><?= Html::encode($self->Name) ?></p>
                        <p><strong>职位：</strong><?= Html::encode($self->Position) ?></p>
                        <p><strong>性别：</strong><?= Html::encode($self->Gender) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>联系方式：</strong><?= Html::encode($self->Contact) ?></p>
                        <p><strong>入职日期：</strong><?= Html::encode($self->HireDate) ?></p>
                        <p class="text-muted">仅可修改联系方式，其余字段为只读。</p>
                        <?= Html::a('查看详情', ['view', 'id' => $self->EmployeeID], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('修改联系方式', ['update', 'id' => $self->EmployeeID], ['class' => 'btn btn-success', 'style' => 'margin-left:8px;']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
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

            $isAdmin ? [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->EmployeeID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','id'=>$model->EmployeeID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','id'=>$model->EmployeeID],[
                        'data'=>['confirm'=>'确认删除该员工？','method'=>'post']
                    ]),
                ],
            ] : [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->EmployeeID]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
    <?php endif; ?>

</div>
