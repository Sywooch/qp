<?php

/* @var $this \yii\web\View */
use yii\bootstrap\Nav;
use app\components\catalog\CatalogRootWidget;
use yii\widgets\Breadcrumbs;

/* @var $content string */
?>
<div class="row">
    <!-- Sidebar-->
    <div class="col-md-3">
        <?=CatalogRootWidget::widget();?>
    </div>
    <!-- End Sidebar-->
    <div class="col-md-9">
        <!-- Main-->
        <div class="page">
            <div class="page-container">
                <div class="hidden-xs">
                    <?=Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ])?>
                </div>
                <?=$content?>
            </div>
        </div>
    </div>
</div>