<?php

use app\components\catalog\ProductWidget;

/** @var app\models\Good\Good $products */
/** @var integer $offset */
/* @var $applied_filters array or null*/
var_dump($applied_filters);

?>

<div class="products-list" data-offset="<?=$offset?>">
    <?php
    if (!empty($products)) {
        foreach ($products as $product) {
            echo ProductWidget::widget([
                'product' => $product,
            ]);
        }
    }
    ?>
</div>

