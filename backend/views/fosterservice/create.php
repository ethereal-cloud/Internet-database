<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */

$this->title = '新增服务';
$this->params['breadcrumbs'][] = ['label' => '服务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterservice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
