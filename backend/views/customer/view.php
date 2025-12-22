<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = '客户详情：' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ((Yii::$app->user->identity->role ?? '') === 'admin'): ?>
        <p>
            <?= Html::a('编辑', ['update', 'id' => $model->CustomerID], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('删除', ['delete', 'id' => $model->CustomerID], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确认删除该客户？',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'CustomerID',
            'user_id',
            'Name',
            'Gender',
            'Contact',
            'Address',
            'MemberLevel',
        ],
    ]) ?>

</div>
