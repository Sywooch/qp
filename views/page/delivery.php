<?php
$this->params['catalog'] = true;
$this->title = "Доставка";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=$this->title?></h1>
<div class="page-static delivery" id="gis-map">
    <p>
        Доставку товара можно осуществить путем обращения в любую организацию,
        предоставляющую услуги курьерской доставки.
        Также вы можете забрать свой заказ самостоятельно в пункте выдачи, по адресу:
        <br><b><?=Yii::$app->params['address']?></b>.
    </p>
    <p>
        Местоположение пункта выдачи указано на карте ниже.
    </p>
<!--        Мы осуществляем доставку по Владивостоку и о. Русский.<br/>-->
<!--        <b>Стоимость доставки зависит от общей стоимости товаров в корзине:</b><br/>-->
<!--        <span class="green"><b>399 руб.</b></span> при сумме заказа <span class="red">от 1 500 до 2 499 руб.</span><br/>-->
<!--        <span class="green"><b>299 руб.</b></span> при сумме заказа <span class="red">от 2 500 до 3 999 руб.</span><br/>-->
<!--        <span class="green"><b>Бесплатная</b></span> при сумме заказа <span class="red">от 4 000 руб.</span><br/>-->
<!--        <span class="red"><b>500 руб.</b></span> при срочной доставке (в течение 3 часов)-->
<!--    <div class="info">-->
<!--        <b>Доставка товаров только с актуальным сроком годности.</b> При сборе Вашего заказа все товары тщательно проверяются нашими специалистами на соответствие сроку годности.-->
<!--    </div><br/>-->
<!--    <div class="info">-->
<!--        <b>Окончательная цена фиксируется в момент подтверждения заказа.</b> Цены товаров не постоянны и зависят от цен поставщиков, а также от конъюнктуры рынка.-->
<!--    </div>-->

    <div class="yandex-map">
        <h3>Карта доставки</h3>
	    <a class="dg-widget-link" href="http://2gis.ru/vladivostok/firm/70000001029417256/center/131.924716,43.151176/zoom/16?utm_medium=widget-source&utm_campaign=firmsonmap&utm_source=bigMap">Посмотреть на карте Владивостока</a><div class="dg-widget-link">
		    <a href="http://2gis.ru/vladivostok/center/131.924716,43.151176/zoom/16/routeTab/rsType/bus/to/131.924716,43.151176╎КУПИ, ООО, интернет-магазин?utm_medium=widget-source&utm_campaign=firmsonmap&utm_source=route">Найти проезд до КУПИ, ООО, интернет-магазин</a></div>
	    <script charset="utf-8" src="https://widgets.2gis.com/js/DGWidgetLoader.js"></script>
	    <script>
          var w = document.getElementById('gis-map').clientWidth;
          window.gisMapWidth = w - 30;
	    </script>
	    <script charset="utf-8">

              new DGWidgetLoader({
                  "width":window.gisMapWidth,"height":500,
                  "borderColor":"#a3a3a3","pos":{"lat":43.151176,"lon":131.924716,"zoom":16},"opt":{"city":"vladivostok"},"org":[{"id":"70000001029417256"}]});


      </script><noscript style="color:#c00;font-size:16px;font-weight:bold;">Виджет карты использует JavaScript. Включите его в настройках вашего браузера.</noscript>
    </div>

</div>

