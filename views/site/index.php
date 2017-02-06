<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/** @var $catalog app\models\Good\Menu  */

use app\components\catalog\CategoryWidget;

$this->title = Yii::$app->name;
$this->params['sidebarLayout'] = true;

?>

<div class="row">
    <?php
    foreach (Yii::$app->db->cache(function ($db) use ($catalog) {
        return $catalog->children(1)->all();
    }) as $ch) {
        echo CategoryWidget::widget([ 'item' => $ch ]);
    }
    ?>
</div>

