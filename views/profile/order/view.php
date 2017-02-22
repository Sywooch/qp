<?php
use app\components\Html;
/* @var $products app\models\OrderProduct*/
/* @var $order app\models\Order*/

$this->title = 'Просмотр заказа';
$this->params['profileLayout'] = true;
$this->params['breadcrumbs'][] = [
    'label' => 'История покупок', 'url' => ['/profile/index']
];
$this->params['breadcrumbs'][] = $this->title;
?>


<h3>Заказ № 123123</h3>
<div class="row">
    <div class="col-sm-6">
        <table class="table">
            <tr>
                <td class="key">Дата зазаза</td>
                <td class="value">2.02.2017 19:30</td>
            </tr>
            <tr>
                <td class="key">Текущий статус</td>
                <td class="value">Выполнен</td>
            </tr>
            <tr>
                <td class="key">Сумма заказа</td>
                <td class="value"><?=Html::price($order->getTotalPrice())?></td>
            </tr>
            <tr>
                <td class="key">Дата зазаза</td>
                <td class="value">2.02.2017 19:30</td>
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
