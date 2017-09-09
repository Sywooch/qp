<?php
use app\models\Good\PropertyValue;
use yii\grid\GridView;
use app\components\Html;
use yii\widgets\ActiveForm;
$this->title = 'Статистика'

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $start, $end integer or null */
?>


<?php $form1 = ActiveForm::begin(['action' => ['report']]) ?>
<label class="control-label">Статистика неподтверждённых товаров за период</label> <br>
<?=
yii\jui\DatePicker::widget([
    'name' => 'start',
    'language' => 'ru',
    'value' => $start,
    'dateFormat' => 'yyyy-MM-dd',
    'clientOptions' => ['value' => date('Y-m-d')],
])
?>
<?=
yii\jui\DatePicker::widget([
    'name' => 'end',
    'language' => 'ru',
    'dateFormat' => 'yyyy-MM-dd',
    'value' => $end,
    'clientOptions' => ['value' => date('Y-m-d')],
])
?>
<button>Вывести</button>
<?php ActiveForm::end() ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
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
        'product_name',
        'product_vendor',
        'product_c1id',
        'count_by_c1id',
        'unconfirmed_count_by_c1id',
    ]
]);
