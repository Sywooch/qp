<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\components\catalog\CatalogRootWidget;
use yii\helpers\Html;

use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\AlertWidget;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <?= $this->render('_header') ?>

    <div class="container">

        <?= AlertWidget::widget() ?>
        <div class="row">
            <!-- Sidebar-->
            <div class="col-md-3">
                <?=CatalogRootWidget::widget();?>
                <div class="filters">
                    <h3>Фильтры</h3>
                </div>
            </div>
            <!-- End Sidebar-->
            <div class="col-md-9">
                <!-- Main-->
                <div class="page">
                    <div class="page-container">
                        <?=Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ])?>
                        <?=$content?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->render('_footer') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
