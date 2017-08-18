<?php
use app\components\Html;

$this->title = 'Как сделать заказ' .
    '';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    #nav-aside {
        padding-top: 25px;
    }
    #nav-aside a {
        color: #767685;
        border-bottom: 1px dotted;
        text-decoration: none;
    }
    #nav-aside a:hover {
        border-bottom-style: solid;
    }
    #nav-aside a.child {
        padding-left: 15px;
    }
    .p-rules ol { counter-reset: item; padding-left: 20px; }
    .p-rules ol > li{ display: block }
    .p-rules ol > li:before { content: counters(item, ".") ". "; counter-increment: item;}
    .p-rules ol > li.ol-title:before { font-size: 20px;}
    .p-rules li > ol > li:before {font-size: 14px;}
    .p-rules h3, .p-rules h2.list_title {
        font-size: 20px;
        display: inline;
        font-weight: bold;
    }
    .ol-title li {
        font-size: 14px;
    }
    ol.ol-root {
        padding-left: 0;
    }
</style>
<div class="page-static p-rules">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-sm-3">
            <div id="nav-aside">

            </div>
        </div>
        <div class="col-sm-8">
            <h2>1. Выбираем товары</h2>
            <p>
                Последовательно перейдите в интересующий вас раздел товаров (например, Кондитерские изделия → Батончики). Откроется страница, где представлены товары с указанием цены, а также фотографией. Нажав на ссылку с наименованием товара, вы сможете перейти к его подробному описанию и характеристикам.</p>

            <h2>2. Кладем товары в корзину</h2>
            <p>
                Выбрав понравившийся товар, нажмите кнопку «Добавить в корзину». Товар автоматически отправится в корзину покупок (можно положить любое количество товаров).
            </p>

            <h2>3. Ваша корзина</h2>

            <p>
                Для оформления заказа перейдите в раздел «Корзина». Просмотрите ее содержимое. Вы можете изменить количество товара или удалить не нужное из корзины покупок.
            </p>
            <h2>4. Оформляем заказ</h2>
            <p>
                Для завершения оформления заказа — необходимо нажать кнопку «Оформить заказ»  ввести свои контактные данные. После нажатия кнопки «Подтвердить», он будет передан нашему менеджеру.
            </p>
            <h2>5. Уточняем детали</h2>
            <p>
                После поступлении заказа, наш менеджер свяжется с вами для уточнения состава заказа.
            <p>Вы также можете самостоятельно связаться с менеджером и оформить заказ по телефону:</p>
            <p>    Телефон для связи <?=Yii::$app->params['phone.manager']?>;</p>
            Приятных покупок!

    </div>
    </div>
</div>

<script>
    (function () {
        HTMLElement.prototype.hasClass = function(cls) {
            var i;
            var classes = this.className.split(" ");
            for(i = 0; i < classes.length; i++) {
                if(classes[i] == cls) {
                    return true;
                }
            }
            return false;
        };

        var nav = document.getElementById('nav-aside'),
            h2 = document.getElementsByTagName('h2'),
            anchor;
        for(var i = 0; i < h2.length; i++) {
            anchor = 'r' + i;
            var a = document.createElement('a');
            a.innerHTML = h2[i].innerText;
            a.setAttribute('href', '#' + anchor);
            h2[i].setAttribute('id', anchor);

            nav.appendChild(a);
            nav.appendChild(document.createElement('br'));
            nav.appendChild(document.createElement('br'));
        }

    })();
</script>