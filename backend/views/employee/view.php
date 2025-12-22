<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */

$this->title = '员工详情：' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => '员工列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->EmployeeID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->EmployeeID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该员工？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'EmployeeID',
            'user_id',
            'Name',
            'Gender',
            'Position',
            'Contact',
            'HireDate',
        ],
    ]) ?>

</div>
