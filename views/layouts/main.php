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
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <?= $this->render('_header') ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>

        <?= AlertWidget::widget() ?>

        <?php
        if (isset($this->params['profileLayout']) && $this->params['profileLayout']) {
            // show sidebar for profile page
            echo $this->render('_profileLayout', ['content' => $content]);
        } else {
            echo $content;
        }
        ?>
    </div>
</div>

<?= $this->render('_footer') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
