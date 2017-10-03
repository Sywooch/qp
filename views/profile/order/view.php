<?php
/* @var $products array of app\models\OrderProduct*/
/* @var $order app\models\Order*/

$this->title = 'Просмотр заказа';
$this->params['profileLayout'] = true;
$this->params['breadcrumbs'][] = [
    'label' => 'История покупок', 'url' => ['/profile/index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-static">
<?=$this->render('/order/_view', [
    'products' => $products,
    'order' => $order,
    'is_owner' => $is_owner,
]); ?>
</div>
