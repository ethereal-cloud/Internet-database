<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '关于我们（About）';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about about-page">
    <div class="download-data" style="margin-bottom: 20px;">
        <?= Html::a('下载 data.zip', Yii::getAlias('@web/data.zip'), ['class' => 'btn btn-success']) ?>
    </div>
    <header class="about-hero">
        <p class="about-kicker">Dogs &amp; Cats</p>
        <h1>欢迎来到Dogs &amp; Cats——一个把“放心”放在第一位的宠物寄养平台。</h1>
        <p class="about-lead">
            我们相信，寄养不只是“有人喂饭遛弯”，更是把家人的信任交到别人手里。
            无论你是短途出差、长假旅行，还是临时有事，我们都希望你的毛孩子在离开你的日子里，
            依然能住得舒适、被温柔照顾、得到足够的陪伴与安全感。
        </p>
    </header>

    <section class="about-section">
        <h2>在Dogs&amp;Cats，你可以为宠物找到合适的寄养师：</h2>
        <ul class="about-list">
            <li>有稳定的照护经验与时间安排</li>
            <li>真实的环境展示与寄养介绍</li>
            <li>清晰的寄养规则与沟通方式</li>
            <li>支持提前见面/视频了解</li>
        </ul>
        <p>
            我们也为寄养师提供更友好的展示与管理工具，让照护变得更专业、更透明，也让每一次托付更安心。
        </p>
    </section>

    <section class="about-section">
        <h2>我们的愿景</h2>
        <p>让每一位宠物家长都能“放心出门”，让每一只宠物都能“像在家一样”。</p>
    </section>

    <section class="about-section">
        <h2>我们的承诺</h2>
        <ul class="about-list">
            <li><strong>透明：</strong>寄养信息清晰可见，沟通更顺畅</li>
            <li><strong>尊重：</strong>尊重每只宠物的习惯、边界与性格</li>
            <li><strong>负责：</strong>遇到问题第一时间沟通与处理</li>
            <li><strong>长期：</strong>把平台做成你愿意一直用的托付选择</li>
        </ul>
    </section>

    <section class="about-quote">
        <blockquote>“把它托付给懂它的人。”</blockquote>
    </section>

    <section class="about-section">
        <h2>我们能为你做什么</h2>
        <div class="about-columns">
            <div>
                <h3>为宠物家长</h3>
                <p>按需选择寄养师和服务类型、自定义寄养时段，定价透明化。</p>
            </div>
            <div>
                <h3>为寄养师</h3>
                <p>展示寄养服务，查看匹配的宠物和客户信息，管理个人资料与订单，全流程更清晰。</p>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2>联系方式</h2>
        <ul class="about-list">
            <li>客服邮箱：2213230@mail.nankai.edu.cn</li>
            <li>客服电话：1866523xxxx（工作时间：【周一至周五 10:00-18:00】）</li>
        </ul>
    </section>

    <section class="about-section about-note">
        <h2>免责声明/提示</h2>
        <p>
            寄养服务由入驻寄养师提供，平台会尽力推动信息真实与沟通透明。
            请在下单前充分沟通宠物健康情况、习惯与注意事项，并确认寄养环境与规则。
        </p>
    </section>
</div>
