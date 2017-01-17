<?php
/* @var $this yii\web\View */
/** @var $catalog app\models\Menu */
use yii\helpers\Url;
use app\components\catalog\CategoryWidget;
use app\components\catalog\ProductWidget;

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
        for ($i = 0; $i < 10; $i++) {
            // CHANGE TO PRODUCT OBJ
            echo ProductWidget::widget([ 'product' => $catalog ]);
        }
    }
    ?>
</div>
