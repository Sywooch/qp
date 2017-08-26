<?php
use app\components\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['profileLayout'] = true;
$this->title = 'Избранное';
$this->params['breadcrumbs'][] = [
    'label' => 'Личный кабинет',
    'url' => ['profile/index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Избранное</h1>

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered'
        ],
        'showHeader' => false,
        'columns' => [
            [   'format' => 'html',
                'contentOptions' => ['class' => 'cell-img'],
                'value' => function ($model) {
                    /* @var $model app\models\Bookmark */
                    return  Html::img([ $model->product->getImgPath()],
                        [ 'height'=>100, 'width'=>100, 'class'=>'img-responsive' ]
                    );
                }
            ],
            [   'format' => 'html',
                'contentOptions' => ['class' => 'cell-description'],
                'value' => function ($model) {
                    /* @var $model app\models\Bookmark */
                    return  Html::a($model->product->name, ['/product/view', 'id' => $model->product->id]);
                }
            ],
            [   'format' => 'raw',
                'contentOptions' => ['class' => 'cell-price'],
                'value' => function ($model) {
                    /* @var $model app\models\Bookmark */
                    return  Html::price($model->product->price).
                        '<br><button class="btn btn-icon btn-icon-left btn-success btn-compare"
                            data-product-id="' . $model->product->id . '"
                            data-product-count="1"
                            data-active="1"' .
                        ($model->product->readyToSale() ?
                            "><span>Купить</span>" :
                            "disabled><span>Недоступен</span>") .
                    '</button>';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'cell-action'],
                'visibleButtons' => [ 'update' => false, 'view' => false],
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url,$model) {
                        /* @var $model app\models\Bookmark */
                        return Html::beginTag('button',[
                                'class' => 'btn remove btn-bookmark',
                                'data-product-id' => $model->product->id,
                                'title' => 'Удалить из избранного',
                                'aria-label' => 'Удалить из избранного'
                        ]). '<i class="fa fa-close"></i>'
                            . Html::endTag('button');
                    },

                ],
            ],
        ],
    ]); ?>
</div>
