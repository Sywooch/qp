<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this app\components\View */
/* @var $model app\models\Good\Good */
/* @var $category app\models\Good\Menu */

$this->title = $model->name;


foreach($category->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] =  [
    'label' => $category->name,
    'url' => Url::to(['catalog/view', 'id' => $category->id])
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="good-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-8">
            <?=Html::img([ $model->getImgPath() ], ['height'=>204, 'width'=>270, 'class'=>'img-responsive'])?>
        </div>
        <div class="col-md-4">
            <label class="product-price">
                <?=$model->convertPrice()?>
            </label>
            <div class="product-panel">
                <div class="btn-group">
                    <input type="number" min="1" value="1"
                           name="product_count"
                           class="product_count"
                           data-product-id="<?= $model->id ?>">
                    <input type="hidden" name="product_id" value=<?= $model->id ?>>
                </div>
                <button class="btn btn-icon btn-icon-left btn-success btn-compare"
                        data-product-id="<?= $model->id ?>"
                        data-product-count="1">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
    <?php
//    DetailView::widget([
//        'model' => $model,
//        'attributes' => [
//            'id',
//            'measure',
//            'c1id',
//            'name',
//            'pic',
//            'price',
//            'category_id',
//            'properties',
//        ],
//    ]) ?>

</div>
