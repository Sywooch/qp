<?php

use app\components\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Панель менеджера';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="manager-password">

    <form action="/manager/secret" method="post">
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
        Секретный ключ заказа:<br>
        <input type="text" name="password">
        <br>
        <input type="submit" value="Отправить">
    </form>

</div><!-- manager-password -->

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'ref',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return Html::a($order->public_id, ['view-order', 'id' => $order->id]);
                }
            ],
            'user.email',
            'created_at:datetime',
            [
                'attribute' => 'total_price',
                'format' => 'raw',
                'value' => function($x) { return Html::unstyled_price($x->total_price); }
            ],
            [
                'attribute' => 'confirmed_price',
                'format' => 'raw',
                'value' => function($x) { return Html::unstyled_price($x->confirmed_price); }
            ],
            'status_str',
        ],
    ]); ?>
</div>
