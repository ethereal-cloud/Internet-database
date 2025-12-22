<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '狗信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dog-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增狗', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'PetID',
            'DogBreedType',
            'TrainingLevel',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => fn($url,$model,$key)=>Html::a('查看',['view','id'=>$model->PetID]),
                    'update' => fn($url,$model,$key)=>Html::a('编辑',['update','id'=>$model->PetID]),
                    'delete' => fn($url,$model,$key)=>Html::a('删除',['delete','id'=>$model->PetID],[
                        'data'=>['confirm'=>'确认删除该记录？','method'=>'post']
                    ]),
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
