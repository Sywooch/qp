<?php
use app\components\Html;
/* @var $products array of sapp\models\OrderProduct*/
/* @var $order app\models\Order*/

$this->title = 'Просмотр заказа';
$this->params['breadcrumbs'][] = [
    'label' => 'Панель менеджера', 'url' => ['/manager']
];
$this->params['breadcrumbs'][] = $this->title;
?>


<h3>Заказ № <?=$order->public_id?> </h3>
<div class="row">
    <div class="col-sm-6">
        <table class="table">
            <tr>
                <td class="key">Дата заказа</td>
                <td class="value"><?=date('d.m.Y', $order->created_at)?></td>
            </tr>
            <tr>
                <td class="key">Текущий статус</td>
                <td class="value"><?=$order->status_str?></td>
            </tr>
            <tr>
                <td class="key">Сумма заказа</td>
                <td class="value"><?=Html::price(array_reduce($products, function($carry, $item) {
                        return $carry + $item->products_count * $item->old_price;
                    }))?></td>
            </tr>
        </table>
    </div>
</div>

<h3>Состав заказа</h3>
<table class="table">
    <tr><th>Товар</th><th>Цена</th><th>Количество</th></tr>
    <?php
    foreach($products as $p) {
        $price = Html::price($p->old_price);
        echo "<tr><td>$p->product_name</td><td>$price</td><td>$p->products_count</td></tr>";
    }
    ?>
</table>
