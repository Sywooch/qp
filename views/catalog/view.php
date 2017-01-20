<?php
/* @var $this yii\web\View */
/** @var $catalog app\models\Good\Menu */
use yii\helpers\Url;
use app\components\catalog\CategoryWidget;
use app\components\catalog\ProductWidget;
use app\models\Good\Good;

$this->title = $catalog->name;
$this->params['sidebarLayout'] = true;
foreach($catalog->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?=$this->title?></h1>

<div class="row">
    <?php
    if ($chs = $catalog->children(1)->all()) {
        foreach ($chs as $ch) {
            echo CategoryWidget::widget([ 'item' => $ch ]);
        }
    }
    else {
        $products = Good::findAll([ 'category_id' => $catalog->id ]);
        foreach ($products as $product) {
            echo ProductWidget::widget([ 'product' => $product ]);
        }
    }
    ?>
</div>
