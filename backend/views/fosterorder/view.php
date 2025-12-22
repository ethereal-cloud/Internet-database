<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */

$this->title = '订单详情：' . $model->OrderID;
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fosterorder-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->OrderID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->OrderID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该订单？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'OrderID',
            [
                'label' => '客户',
                'value' => $model->CustomerID,
            ],
            [
                'label' => '宠物',
                'value' => $model->pet->PetName ?? $model->PetID,
            ],
            [
                'label' => '服务',
                'value' => $model->service ? $model->service->ServiceType . ' / ' . $model->service->PetCategory : $model->ServiceID,
            ],
            'StartTime',
            'EndTime',
            'OrderStatus',
            'PaymentAmount',
            [
                'label' => '负责员工',
                'value' => function($model) {
                    $names = array_map(function($emp) {
                        $contact = $emp->Contact ? '（' . $emp->Contact . '）' : '';
                        return $emp->Name . $contact;
                    }, $model->employees ?? []);
                    return $names ? implode('、', $names) : '-';
                },
            ],
        ],
    ]) ?>

</div>
