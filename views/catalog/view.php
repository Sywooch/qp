<?php
/* @var $this yii\web\View */
/** @var $catalog app\models\Good\Menu */
use yii\helpers\Url;
use app\components\catalog\CategoryWidget;

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
    foreach ($catalog->children(1)->all() as $ch) {
        echo CategoryWidget::widget([ 'item' => $ch ]);
    }
    ?>
</div>
