<?php

/* @var $this \yii\web\View */
use yii\bootstrap\Nav;
use app\components\catalog\CatalogWidget;
/* @var $content string */
?>
<div class="row">
    <!-- Sidebar-->
    <div class="col-md-3">
        <?=CatalogWidget::widget();?>
    </div>
    <!-- End Sidebar-->
    <div class="col-md-9">
        <!-- Main-->
        <div class="page">
            <div class="page-container">
                <?=$content?>
            </div>
        </div>
    </div>
</div>