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

    <?php if ((Yii::$app->user->identity->role ?? '') === 'admin'): ?>
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
    <?php endif; ?>

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
            [
                'label' => '类别',
                'value' => function ($model) {
                    if ($model->cat) {
                        return '猫（' . $model->cat->FurLength . ' / ' . $model->cat->Personality . '）';
                    }
                    if ($model->dog) {
                        return '狗（' . $model->dog->DogBreedType . ' / ' . $model->dog->TrainingLevel . '）';
                    }
                    return '未知';
                },
            ],
        ],
    ]) ?>

</div>
