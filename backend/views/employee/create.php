<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */

$this->title = '新增员工';
$this->params['breadcrumbs'][] = ['label' => '员工列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
