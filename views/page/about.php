<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'О компании';
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->params;
$raw_phone = preg_replace('/ |-|\(|\)/', '', $params['phone']);
?>
<div class="page-static site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <h2>РЕКВИЗИТЫ ПРОДАВЦА</h2>
    <p>Продавец: <?=$params['shop']?></p>
    <p>Телефон: <a href="tel:<?=$raw_phone?>"><?=$params['phone']?></a></p>
    <p>Адрес: <?=$params['address']?></p>
    <p>Юридический адрес: <?=$params['law_address']?></p>
    <p>ИНН/КПП: <?=$params['inn']?></p>
    <p>ОГРН: <?=$params['ogrn']?></p>
    <h3>Банковские реквизиты:</h3>
    <p>Наименование Банка: <?=$params['bank']?></p>
    <p>К/с: <?=$params['ks']?></p>
    <p>Бик: <?=$params['bik']?></p>
    <p>Р/с: <?=$params['rs']?></p>
    <br/>
</div>
