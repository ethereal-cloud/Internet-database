<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $services \common\models\Fosterservice[] */

$this->title = '寄养服务';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-services">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Html::encode($service->ServiceType) ?> · <?= Html::encode($service->PetCategory) ?>
                    </div>
                    <div class="panel-body">
                        <p class="lead">¥<?= Html::encode($service->Price) ?>/<?= Html::encode($service->Duration) ?>天</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p>
        <?= Html::a('返回工作台', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('查看我的订单', ['orders'], ['class' => 'btn btn-info']) ?>
    </p>
</div>
