<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Pet */

$this->title = '宠物详情：' . $model->PetID;
$this->params['breadcrumbs'][] = ['label' => '宠物列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pet-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->PetID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->PetID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该宠物？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'PetID',
            'CustomerID',
            'PetName',
            'Gender',
            'AgeYears',
            'AgeMonths',
            'HealthStatus',
        ],
    ]) ?>

</div>
