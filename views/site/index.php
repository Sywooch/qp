<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/** @var $catalog app\models\Good\Menu  */

$this->title = Yii::$app->name;
$this->params['sidebarLayout'] = true;

?>

<div class="row">
    <?php
    foreach ($catalog->children(1)->all() as $ch) {
        echo \app\components\catalog\CategoryWidget::widget([ 'catalog_item' => $ch ]);
    }
    ?>
</div>

