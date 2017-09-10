<?php
use app\models\Good\PropertyValue;
use kartik\grid\GridView;
use app\components\Html;
use yii\widgets\ActiveForm;
$this->title = 'Статистика'

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $start, $end integer or null */
?>

<style>
    .date-picker > div:first-child {
        padding-left: 15px;
    }
    .date-picker > div {
        padding-left: 2px;
        padding-right: 2px;
    }
</style>
<div class="box box-primary">
    <?php $form1 = ActiveForm::begin(['action' => ['report']]) ?>
    <div class="box-header with-border">
        <h3 class="box-title">Фильтры</h3>
    </div>
    <div class="box-body">
        <p>Статистика неподтверждённых товаров за период</p>
        <div class="row date-picker">
            <div class="col-sm-2">
                <?= yii\jui\DatePicker::widget([
                    'name' => 'start',
                    'language' => 'ru',
                    'value' => $start,
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => ['value' => date('Y-m-d')],
                    'options' => ['class' => 'form-control']
                ]) ?>
            </div>
            <div class="col-sm-2">
                <?= yii\jui\DatePicker::widget([
                    'name' => 'end',
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',
                    'value' => $end,
                    'clientOptions' => ['value' => date('Y-m-d')],
                    'options' => ['class' => 'form-control']
                ]) ?>
            </div>
            <div class="col-sm-2">
                  <button class="btn btn-primary">Вывести</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'export' => false,
    'responsive' => true,
    'hover' => true,
    'columns' => [
        [
            'attribute' => 'provider',
            'label' => 'Поставщик',
            'value' => function($model) {
                /* @var $model app\models\Good\Good */
                return PropertyValue::cachedFindOne(['c1id' => $model->provider])->value;
            },
            'enableSorting'=>TRUE,
        ],
        [
            'attribute' => 'old_price',
            'label' => 'Цена, р.',
            'value' => function($model) {
                /* @var $model app\models\Good\Good */
                return Html::unstyled_price($model->old_price);
            },
            'enableSorting'=>TRUE,
        ],
        [
            'attribute' => 'product_name',
            'value' => function ($model) {
                return $model->product_name;
            },
        ],
        'product_vendor',
        [
            'attribute' => 'product_c1id',
            'value' => function ($model) {
                return $model->product_c1id;
            },
        ],
        'count_by_c1id',
        'unconfirmed_count_by_c1id',
    ]
]);
