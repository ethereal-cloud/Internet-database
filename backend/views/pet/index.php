<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\PetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '宠物列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pet-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增猫', ['create', 'type' => 'cat'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('新增狗', ['create', 'type' => 'dog'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php Pjax::begin(); ?>

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

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $type = $model->cat ? 'cat' : ($model->dog ? 'dog' : 'cat');
                        return Html::a('编辑', ['update', 'id' => $model->PetID, 'type' => $type]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('查看', ['view', 'id' => $model->PetID]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('删除', ['delete', 'id' => $model->PetID], [
                            'data' => [
                                'confirm' => '确认删除该宠物？',
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
