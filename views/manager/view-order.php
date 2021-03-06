<?php
/* @var $products array of sapp\models\OrderProduct*/
use app\assets\ManagerAsset;

/* @var $order app\models\Order*/
/* @var $is_owner bool */

$this->title = 'Просмотр заказа';
$this->params['breadcrumbs'][] = [
    'label' => 'Панель менеджера', 'url' => ['/manager']
];
$this->params['breadcrumbs'][] = $this->title;
ManagerAsset::register($this);
?>

<div class="page-static">
    <button class="btn js-print-order" style="float: right;" data-order-id="<?=$order->id?>">Сохранить в файл</button>
    <?=$this->render('/order/_view', [
        'products' => $products,
        'order' => $order,
        'is_owner' => false,
    ]); ?>
</div>
