<?php
/* @var $this yii\web\View */
/** @var $catalog app\models\Good\Menu */
use yii\helpers\Url;
use app\components\catalog\CategoryWidget;
use yii\caching\TagDependency;
use app\models\Good\Menu;

$this->title = $catalog->name;
$this->params['sidebarLayout'] = true;
foreach(Yii::$app->db->cache(function ($db) use($catalog)
{
    return $catalog->parents()->all();
}, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $par) {
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
    foreach(Yii::$app->db->cache(function ($db) use($catalog)
    {
        return $catalog->children(1)->all();
    }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $ch) {
        echo CategoryWidget::widget([ 'item' => $ch ]);
    }
    ?>
</div>
