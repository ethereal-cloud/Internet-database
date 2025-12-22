<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $customer \common\models\Customer */
/* @var $pets \common\models\Pet[] */
/* @var $filters array */

$this->title = '宠物信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-pets">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">客户：<?= Html::encode($customer->Name ?: $customer->CustomerID) ?></p>

    <div class="panel panel-default">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <?= Html::beginForm(['pets'], 'get', ['class' => 'form-inline']) ?>
                <div class="form-group">
                    <?= Html::label('类型', 'filter-type', ['class' => 'control-label']) ?>
                    <?= Html::dropDownList('type', $filters['type'], [
                        '' => '全部',
                        'cat' => '猫',
                        'dog' => '狗',
                    ], ['class' => 'form-control', 'id' => 'filter-type']) ?>
                </div>
                <div class="form-group" style="margin-left:10px;">
                    <?= Html::label('性别', 'filter-gender', ['class' => 'control-label']) ?>
                    <?= Html::dropDownList('gender', $filters['gender'], [
                        '' => '全部',
                        '公' => '公',
                        '母' => '母',
                    ], ['class' => 'form-control', 'id' => 'filter-gender']) ?>
                </div>
                <div class="form-group" style="margin-left:10px;">
                    <?= Html::textInput('q', $filters['q'], ['class' => 'form-control', 'placeholder' => '按名字模糊搜索']) ?>
                </div>
                <div class="form-group" style="margin-left:10px;">
                    <?= Html::textInput('health', $filters['health'], ['class' => 'form-control', 'placeholder' => '按健康状况']) ?>
                </div>
                <?= Html::submitButton('筛选', ['class' => 'btn btn-primary', 'style' => 'margin-left:10px;']) ?>
                <?= Html::a('重置', ['pets'], ['class' => 'btn btn-default', 'style' => 'margin-left:5px;']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>宠物ID</th>
                    <th>名字</th>
                    <th>类别</th>
                    <th>性别</th>
                    <th>年龄</th>
                    <th>健康状况</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $pet): ?>
                    <?php
                        $type = $pet->cat ? '猫' : ($pet->dog ? '狗' : '未知');
                        $extra = '';
                        if ($pet->cat) {
                            $extra = $pet->cat->FurLength . ' / ' . $pet->cat->Personality;
                        } elseif ($pet->dog) {
                            $extra = $pet->dog->DogBreedType . ' / ' . $pet->dog->TrainingLevel;
                        }
                    ?>
                    <tr>
                        <td><?= Html::encode($pet->PetID) ?></td>
                        <td><?= Html::encode($pet->PetName) ?></td>
                        <td><?= Html::encode($type) ?><?= $extra ? '（' . Html::encode($extra) . '）' : '' ?></td>
                        <td><?= Html::encode($pet->Gender) ?></td>
                        <td><?= Html::encode($pet->AgeYears . '岁' . $pet->AgeMonths . '月') ?></td>
                        <td><?= Html::encode($pet->HealthStatus) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p>
        <?= Html::a('返回工作台', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('查看订单', ['orders'], ['class' => 'btn btn-info']) ?>
    </p>
</div>
