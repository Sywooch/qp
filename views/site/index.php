<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/** @var $catalog app\models\Good\Menu  */
/** @var $products app\models\Good\Good  */
/** @var $stats array */
/** @var $is_discount */
use app\components\catalog\CategoryWidget;
use app\components\catalog\ProductWidget;
use app\components\Html;

$this->title = Yii::$app->name;
$this->params['catalog'] = true;
$tomorrow = date("d m", strtotime("+1 day"));
?>
<div class="homepage">
    <section class="top-product">
        <div class="section-title">
            <?= $is_discount ? 'Товары со скидкой' : 'Популярные товары' ?>
        </div>
        <div class="row">
            <?php
            foreach($products as $product) {
                echo ProductWidget::widget([
                    'product' => $product,
                ]);
            }
            ?>
        </div>
    </section>
    <section class="stat">
        <div class="section-title">
            Статистика
        </div>
        <div class="row stat__list">
            <?=$this->render('_stat', [
                'icon' => 'icons/deal.png',
                'number' => $stats['orders'],
                'text' => 'сделок совершено'
            ])?>
            <?=$this->render('_stat', [
                'icon' => 'icons/shopping-cart.png',
                'number' => $stats['products'],
                'text' => 'товаров продано'
            ])?>
            <?=$this->render('_stat', [
                'icon' => 'icons/like.png',
                'number' => $stats['clients'],
                'text' => 'клиентов нам доверяют'
            ])?>
        </div>
    </section>
</div>
