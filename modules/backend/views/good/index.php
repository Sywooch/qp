<?php

use yii\grid\GridView;
use app\components\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin(['action' => ['check-status']]) ?>
<label class="control-label">Попытаться установить статус ОК всем товарам со статусом ОШИБКА</label> <br>
<button>Установить</button>
<?php ActiveForm::end() ?>

<div class="good-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'price',
                'label' => 'Цена, р.',
                'value' => function($model) {
                    /* @var $model app\models\Good\Good */
                    return Html::unstyled_price($model->getPrice());
                },
                'enableSorting'=>TRUE,
            ],
            [
                'attribute' => 'category_id',
                'label' => 'Категория',
                'value' => function($model) {
                /* @var $model app\models\Good\Good */
                    return $model->category->name;
                },
                'enableSorting'=>TRUE,
            ],
//            'is_discount',
            'vendor',
            'c1id',
            [
                'attribute' => 'provider',
                'label' => 'Поставщик',
                'value' => function($model) {
                    /* @var $model app\models\Good\Good */
                    return $model->getProviderName();
                },
                'enableSorting'=>TRUE,
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'value' => function($model) {
                    /* @var $model app\models\Good\Good */
                    return $model->getStatusString();
                },
                'enableSorting'=>TRUE,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            $url);
                    },
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-edit"></i>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>',
                            $url,  [
                                'data' => [
                                    'confirm' => 'Вы уверены?',
                                    'method' => 'post',
                                ]
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
