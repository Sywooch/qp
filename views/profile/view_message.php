<?php
use app\components\Html;
use app\models\Order;

/* @var $message app\models\profile\Message*/

$this->title = 'Просмотр заказа';
$this->params['profileLayout'] = true;
$this->params['breadcrumbs'][] = [
    'label' => 'Сообщения', 'url' => ['/profile/message']
];
$this->params['breadcrumbs'][] = $this->title;
?>


<h3><?=date('d.m.Y h:m', $message->created_at);?> </h3>
<?= $message->text ?>

