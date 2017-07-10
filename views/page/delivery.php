<?php
$this->params['catalog'] = true;
$this->title = "Доставка";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=$this->title?></h1>
<div class="page-static delivery">
    <p>
        Мы осуществляем доставку по Владивостоку и о. Русский.<br/>
        <b>Стоимость доставки зависит от общей стоимости товаров в корзине:</b><br/>
        <span class="green"><b>399 руб.</b></span> при сумме заказа <span class="red">от 1 500 до 2 499 руб.</span><br/>
        <span class="green"><b>299 руб.</b></span> при сумме заказа <span class="red">от 2 500 до 3 999 руб.</span><br/>
        <span class="green"><b>Бесплатная</b></span> при сумме заказа <span class="red">от 4 000 руб.</span><br/>
        <span class="red"><b>500 руб.</b></span> при срочной доставке (в течение 3 часов)
    </p>
    <div class="info">
        <b>Доставка товаров только с актуальным сроком годности.</b> При сборе Вашего заказа все товары тщательно проверяются нашими специалистами на соответствие сроку годности
    </div><br/>
    <div class="info">
        <b>Окончательная цена фиксируется в момент подтверждения заказа.</b> Цены товаров не постоянны и зависят от цен поставщиков, а также от конъюнктуры рынка.
    </div>
</div>

<div class="yandex-map">
    <h3>Карта доставки</h3>
    <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=xIJW017IJvnxZeKpt_C00CQYFPqXIkj-&amp;width=687&amp;height=452&amp;lang=ru_RU&amp;sourceType=constructor&amp;scroll=true"></script>
</div>
