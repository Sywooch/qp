<?php

/* @var $this \yii\web\View */
/* @var $content string */

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
    <link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <?= Html::csrfMetaTags() ?>
    <script src="https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js"></script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?= $this->render('_header') ?>

    <div class="container">

        <?= AlertWidget::widget() ?>

        <?php
        if (isset($this->params['profileLayout']) && $this->params['profileLayout']) {
            // show sidebar for profile page
            echo $this->render('_profileLayout', ['content' => $content]);

        } elseif (isset($this->params['catalog']) && $this->params['catalog']) {

            echo $this->render('_catalog', ['content' => $content]);

        } elseif (isset($this->params['nullLayout']) && $this->params['nullLayout']) {

            echo $this->render('_null', ['content' => $content]);

        } else {
            ?>
            <div class="hidden-xs hidden-sm">
                <?php
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);
                ?>
            </div>
            <?= $content; ?>
        <?php } ?>
    </div>
</div>

<?= $this->render('_footer') ?>
<?= $this->render('_search_modal') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
