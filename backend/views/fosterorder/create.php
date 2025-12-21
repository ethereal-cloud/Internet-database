<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fosterorder */

$this->title = 'Create Fosterorder';
$this->params['breadcrumbs'][] = ['label' => 'Fosterorders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fosterorder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
