<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\PetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pet-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增猫', ['create', 'type' => 'cat'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('新增狗', ['create', 'type' => 'dog'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'PetID',
            'CustomerID',
            'PetName',
            'Gender',
            'AgeYears',
            //'AgeMonths',
            //'HealthStatus',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $type = $model->cat ? 'cat' : ($model->dog ? 'dog' : 'cat');
                        return Html::a('Update', ['update', 'id' => $model->PetID, 'type' => $type]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', ['view', 'id' => $model->PetID]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Delete', ['delete', 'id' => $model->PetID], [
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
