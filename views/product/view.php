<?php

use app\components\Html;
use app\models\Bookmark;
use yii\web\View;
use yii\helpers\Url;
use yii\caching\TagDependency;
use app\models\Good\Menu;
use app\models\Good\PropertyValue;
use app\models\Good\GoodProperty;

/* @var $this yii\web\View */
/* @var $product app\models\Good\Good */
/* @var $category app\models\Good\Menu */

$this->title = $product->name;

foreach(Yii::$app->db->cache(function() use($category) {
    return $category->parents()->all();
}, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}

$this->params['breadcrumbs'][] =  [
    'label' => $category->name,
    'url' => Url::to(['catalog/view', 'id' => $category->id])
];
$this->params['breadcrumbs'][] = $this->title;



$bookmark = Bookmark::cachedFindOne([
    'product_id' => $product->id,
    'user_id' => Yii::$app->user->getId()
]);
if (!$bookmark) {
    $bookmark = new Bookmark([
        'user_id' => Yii::$app->user->getId(),
        'product_id' => $product->getId(),
    ]);
}
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="product-view page-static">
    <div class="row">
        <div class="col-sm-4 col-xs-12">
            <div class="thumbnails">
                <div class="product-image">
                    <?=Html::img([ $product->getImgPath() ], ['height'=>204, 'width'=>270, 'class'=>'img-responsive', 'data-product-id'=>$product->id])?>
                </div>
            </div>
            <?php if(\Yii::$app->user->can('admin')) : ?>
                <?= Html::a('Просмотреть в панели администратора',
                    ['backend/good/view', 'id' => $product->id], [
                    ])
                ?>
            <?php endif; ?>
        </div>
        <div class="col-sm-8 col-xs-12 product-detail">

            <div class="price">
                <?=Html::price($product->price)?>
            </div>
            <div class="product__panel" data-toggle="buttons">
                <div class="btn-group">
                    <input type="number" min="1" value="1"
                           name="product_count"
                           class="product_count"
                           data-product-id="<?= $product->id ?>">
                    <input type="hidden" name="product_id" value=<?= $product->id ?>>
                </div>
                <button class="btn btn-icon btn-icon-left btn-success btn-compare"
                        data-product-id="<?= $product->id ?>"
                        data-product-count="1"
                        <?=$product->readyToSale() ?
                            "><span><i class=\"fa fa-shopping-cart\" aria-hidden=\"true\"></i>Купить</span>" :
                            "disabled><span>Недоступен</span>"
                        ?>
                </button>

                <button class="btn product-to-bookmark btn-default bookmark <?=$bookmark->isNewRecord ? '' : 'active'?>"
                        data-product-id="<?= $product->id ?>"
                        data-placement="top"
                        title="<?=$bookmark->isNewRecord ? 'В избранное' : 'В избранном'?>">
                    <span class="icon lnr lnr-heart"></span>
                    <span class="counter"><?= $product->getBookmarksCount() ? $product->getBookmarksCount() : '' ?></span>
                    <input type="checkbox">
                </button>

            </div>

            <div class="product__params">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab-description" aria-controls="home" role="tab" data-toggle="tab">Описание</a></li>
                    <li role="presentation"><a href="#tab-param" aria-controls="profile" role="tab" data-toggle="tab">Характеристики</a></li>
                    <li role="presentation" class="hidden-xs"><a href="#tab-delivery" aria-controls="messages" role="tab" data-toggle="tab">Доставка и оплата</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab-description">
                        <p>Тут будет описание?</p>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-param">
                        <ul class="product__params-list">
                            <?php
                            foreach ($product->safeProperties as $key => $value) {
                                echo "<li class='item'><span class='item-key'>" . GoodProperty::cachedFindOne($key)->name . "</span>"
                                    . "<span class='item-value'>" . PropertyValue::cachedFindOne($value)->value . "</span></li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div role="tabpanel" class="tab-pane hidden-xs" id="tab-delivery">
                        <p>
                            Мы осуществляем доставку по Владивостоку и о. Русский.<br/>
                        <b>Стоимость доставки зависит от общей стоимости товаров в корзине:</b><br/>
                            <span class="green"><b>399 руб.</b></span> при сумме заказа <span class="red">от 1 500 до 2 499 руб.</span><br/>
                            <span class="green"><b>299 руб.</b></span> при сумме заказа <span class="red">от 2 500 до 3 999 руб.</span><br/>
                            <span class="green"><b>Бесплатная</b></span> при сумме заказа <span class="red">от 4 000 руб.</span><br/>
                            <span class="red"><b>500 руб.</b></span> при срочной доставке (в течение 3 часов)
                        </p>
                        <div class="info">
                            <b>Доставка товаров только с актуальным сроком годности.</b> При сборе Вашего заказа все товары тщательно проверяются нашими специалистами на соответствие сроку годности
                        </div><br/>
                        <div class="info">
                            <b>Окончательная цена фиксируется в момент подтверждения заказа.</b> Цены товаров не постоянны и зависят от цен поставщиков, а также от конъюнктуры рынка.
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
