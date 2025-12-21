<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterservice */

$this->title = 'Create Fosterservice';
$this->params['breadcrumbs'][] = ['label' => 'Fosterservices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterservice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
