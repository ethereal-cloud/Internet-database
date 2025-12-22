<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Dog */

$this->title = '狗详情：' . $model->PetID;
$this->params['breadcrumbs'][] = ['label' => '狗信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="dog-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->PetID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->PetID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该记录？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'PetID',
            'DogBreedType',
            'TrainingLevel',
        ],
    ]) ?>

</div>
