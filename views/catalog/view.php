<?php
/* @var $this yii\web\View */
/** @var $catalog app\models\Menu */
use yii\helpers\Url;

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
    for($i = 0; $i < 10; $i++) {
        echo \app\components\product\ProductWidget::widget();
    }

    ?>
</div>
