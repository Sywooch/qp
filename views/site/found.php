<?php
use yii\helpers\Html;
use app\components\catalog\ProductWidget;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var app\models\Good\Good $products */
/* @var app\models\Good\Menu $categories */
/* @var $categoryDataProvider yii\data\ArrayDataProvider */
/* @var $query string */

$this->title = "Результаты поиска";
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>Результат поиска по запросу: "<?=$query?>"</h1>
<?php if (!empty($categories)) : ?>
    <h2>Категории</h2>
    <div class="catalog">
        <ul class="nav nav-stacked mate__nav">
            <?php
            foreach ($categories as $category) {
                echo "<li>".Html::a($category->name, ['/catalog/view', 'id' => $category->id])."</li>";
            }
            ?>
        </ul>
    </div>
<?php endif; ?>

<h2>Товары</h2>
<div class="products-list">
    <?php
    if (!empty($products)) {
        foreach ($products as $product) {
            echo ProductWidget::widget([
                'product' => $product,
            ]);
        }
    }
    ?>
</div>

