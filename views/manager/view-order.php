<?php
/* @var $products array of sapp\models\OrderProduct*/
/* @var $order app\models\Order*/

$this->title = 'Просмотр заказа';
$this->params['breadcrumbs'][] = [
    'label' => 'Панель менеджера', 'url' => ['/manager']
];
$this->params['breadcrumbs'][] = $this->title;
?>

<?=$this->render('/order/_view', [
    'products' => $products,
    'order' => $order,
    'is_owner' => false,
]); ?>