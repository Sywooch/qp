<?php

use app\components\catalog\ProductWidget;

/** @var app\models\Good\Good $products */
/** @var integer $offset */

?>
<div class="products-list" data-offset="<?=$offset?>">
    <?php
    foreach ($products as $product) {
        echo ProductWidget::widget([
            'product' => $product,
        ]);
    }
    ?>
</div>

