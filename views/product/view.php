<?php

use app\components\Html;
use app\models\Bookmark;
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

<h1 class="product-title"><?= Html::encode($this->title) ?></h1>

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
            <div class="row">
                <div class="col-sm-8">
                    <div class="price">
                        <?=Html::price($product->price)?>
                    </div>
                    <div class="product__panel" data-toggle="buttons">
                        <div class="btn-group сщд-">
                            <input type="number" min="1" value="1"
                                   name="product_count"
                                   class="product_count"
                                   data-product-id="<?= $product->id ?>">
                            <input type="hidden" name="product_id" value=<?= $product->id ?>>
                        </div>
                        <div class="btn-group">
                        <?php if($product->readyToSale()) : ?>
                            <button class="btn product-to-cart btn-success btn-compare"
                                    data-product-id="<?= $product->id ?>"
                                    data-product-count="1"
                                    data-active="1">
                                В корзину
                            </button>
                        <?php else: ?>
                            <span class="product-disabled">Нет в наличии</span>
                        <?php endif; ?>
                        </div>
                        <div class="btn-group">
                            <button class="btn product-to-bookmark btn-default bookmark <?=$bookmark->isNewRecord ? '' : 'active'?>"
                                    data-product-id="<?= $product->id ?>"
                                    data-placement="top"
                                    title="<?=$bookmark->isNewRecord ? 'В избранное' : 'В избранном'?>">
                                <span class="icon lnr lnr-heart"></span>
                                <span class="counter"><?= $product->getBookmarksCount() ? $product->getBookmarksCount() : '' ?></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="product__delivery hidden-xs">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <?php if ($product->readyToSale()) : ?>
                            <tr>
                                <td class="product__delivery-status available">
                                    <i class="fa fa-check fa-lg"></i> Товар в наличии
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-truck fa-lg"></i> Доставим:</td>
                            </tr>
                            <tr>
                                <td><?=Html::dateRu(date("d m", strtotime("+1 day")))?> - <?=Html::dateRu(date("d m", strtotime("+2 day")))?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px;">(по Вашему выбору)</td>
                            </tr>
                            <?php else : ?>
                                <tr>
                                    <td class="product__delivery-status unavailable">
                                        <span class="lnr lnr-sad"></span> Товар не доступен
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>

                    </div>
                </div>
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
