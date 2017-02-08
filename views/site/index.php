<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/** @var $catalog app\models\Good\Menu  */

use app\components\catalog\CategoryWidget;

$this->title = Yii::$app->name;
$this->params['sidebarLayout'] = true;

?>

<section class="schedule">
    <div class="section-title">
        Время доставки заказа
    </div>
    <div class="row schedule__list">
        <?=$this->render('_schedule', [
            'day' => 'Сегодня',
            'date' => date('d M'),
            'status' => false
        ])?>
        <?=$this->render('_schedule', [
            'day' => 'Завтра',
            'date' => date('d M'),
            'status' => true
        ])?>
    </div>
</section>
<section class="top-product">
    <div class="section-title">
        Популярные товары
    </div>
</section>
<section class="stat">
    <div class="section-title">
        Статистика
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="stat__"></div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12"></div>
        <div class="col-md-4 col-sm-4 col-xs-12"></div>
    </div>
</section>

