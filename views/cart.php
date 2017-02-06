<?php

use app\components\Html;
use yii\grid\GridView;

/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ArrayDataProvider */
/** @var $cart \yz\shoppingcart\ShoppingCart */

$this->title = 'Корзина';
?>
<main class="cart app" data-stage="cart">
    <h1><?= Html::encode($this->title) ?></h1>
<?php if($dataProvider->getTotalCount()) : ?>

    <div class="cart-list">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn'
                ], [
                    'format' => 'html',
                    'value' => function ($product) {
                        /* @var $product app\models\Good\Good */
                        return  Html::img([ $product->getImgPath() ],
                            [ 'height'=>100, 'width'=>100, 'class'=>'img-responsive' ]
                        );
                    }
                ],
                'name',
                [
                    'attribute' => 'Количество',
                    'format' => 'raw',
                    'value' => function ($product) {
                        /* @var $product app\models\Good\Good */
                        return  Html::stepper($product->id, $product->getQuantity());
                    }
                ], [
                    'attribute' => 'Цена',
                    'format' => 'raw',
                    'value' => function ($product) {
                        /* @var $product app\models\Good\Good */
                        return $this->render('/order/_itemPrice', ['product' => $product]);
                    }
                ], [
                    'class' => 'yii\grid\ActionColumn',
                    'visibleButtons' => [ 'update' => false, 'view' => false],
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url,$model) {
                            return Html::a(
                                '<i class="fa fa-close"></i>',
                                $url, ['class' => 'cart-delete', 'data-method' => 'post', 'title' => 'Удалить', 'aria-label' => 'Удалить']);
                        },

                    ],
                ],
            ]
        ]) ?>
        <div class="cart-total">
            <span class="label">
                Итого:
            </span>
            <span id="total" class="price">
                <?=Html::price($cart->getCost())?>
            </span>
        </div>
    </div>
    <div class="cart-btn">
        <?= Html::a('Оформить заказ', ['order'], [
            'class' => 'btn btn-success btn-lg ',
        ]) ?>
    </div>

<?php else: ?>
    <div class="cart-empty">

        <?=Html::img('@web/img/components/cart-empty.gif');?>
        <h2>Корзина пуста</h2>
        <br/>
        <p><b>В корзине нет ни одного товара или услуги</b></p>
        <p>Если вы считаете, что это ошибка, обратитесь в IT-отдел компании: <?=Yii::$app->params['phone.it']?></p>
    </div>
<?php endif; ?>

</main>
