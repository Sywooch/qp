<?php

use app\components\catalog\ProductWidget;

/** @var app\models\Good\Good $products */
foreach ($products as $product) {
    echo ProductWidget::widget([
        'product' => $product,
    ]);
}