<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $form yii\bootstrap\ActiveForm */
/** @var $model app\models\Menu  */

$this->title = Yii::$app->name;
$this->params['sidebarLayout'] = true;

?>

<?php
for($i = 0; $i < 10; $i++) {
    echo \app\components\product\ProductWidget::widget();
}

?>
