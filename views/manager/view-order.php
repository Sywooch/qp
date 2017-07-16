<?php
/* @var $products array of sapp\models\OrderProduct*/
/* @var $order app\models\Order*/

$this->title = 'Просмотр заказа';
$this->params['breadcrumbs'][] = [
    'label' => 'Панель менеджера', 'url' => ['/manager']
];
$this->params['breadcrumbs'][] = $this->title;
ManagerAsset::register($this);
?>

<div class="page-static">
    <button class="btn js-print-order" style="float: right;">Сохранить в файл</button>
    <?=$this->render('/order/_view', [
        'products' => $products,
        'order' => $order,
        'is_owner' => false,
    ]); ?>
</div>
