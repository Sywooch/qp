<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\catalog\ProductWidget;
use app\models\Good\Good;

/* @var $this yii\web\View */
/* @var $category app\models\Good\Menu */
/* @var $products array app\models\Good\Menu */
/* @var $filters null or array [ prop_name => [ 'value' => [values], 'type' => type ]] */

$this->title = $category->name;
foreach($category->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="good-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
        foreach ($products as $product) {
            echo ProductWidget::widget([
                'product' => $product,
            ]);
        }
    ?>
</div>
