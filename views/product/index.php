<?php

use app\components\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use app\components\catalog\ProductWidget;
use app\models\Good\Good;
use yii\caching\TagDependency;
use app\models\Good\Menu;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $category app\models\Good\Menu */
/* @var $mate app\models\Good\Menu */
/* @var parentID integer */
/* @var $products array of app\models\Good\Good */
/* @var $filters null or array [ prop_name => [ 'value' => [values], 'type' => type ]] */

$this->title = $category->name;

$this->params['nullLayout'] = true;

foreach(Yii::$app->db->cache(function ($db) use($category)
{
    return $category->parents()->all();
}, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-sm-3">
        <div class="filters">
            <?=\app\components\catalog\CatalogMateWidget::widget([
                    'catalog' => $category
            ])?>
            <h3>Фильтры</h3>
        </div>
    </div>
    <div class="col-sm-9">
        <?php
        echo Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
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
    </div>
</div>

