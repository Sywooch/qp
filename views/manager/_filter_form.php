<?php
/**
 * @var $model app\models\OrderFilterForm
 */
use app\models\Order;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;

$today = date('Y-m-d');
$yesterday = date("Y-m-d", time() - 60 * 60 * 24);
$get = Yii::$app->request->get();

$interval = isset($get['after']) && isset($get['before']) ? $get['after'] : '';

?>

<div class="filter__item filter-key">
    <span class="filter__item-title">Секретный ключ</span>
    <div class="text-subline"></div>
    <div class="filter__item-prop input-group-custom">
        <div class="manager-password">
            <form action="/manager/secret" method="post">
                <div>
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                    <input type="text" name="password" class="form-control col-xs-10" placeholder="Секретный ключ заказа">
                </div>
                <div>
                    <button type="submit" class="btn">Отправить</button>
                </div>
            </form>
        </div><!-- manager-password -->
    </div>
</div>


<?php
$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['/manager'],
    'options' => [
        'class' => 'datepicker-form'
    ]
]);
?>

<div class="filter__item">
    <span class="filter__item-title">Дата</span>
    <div class="text-subline"></div>
    <div class="filter__item-prop input-group-custom">
        <input type="hidden" name="after" class="manager-date-start" value=<?=$model->after?>/>
        <input type="hidden" name="before" class="manager-date-end" value=<?=$model->before?>/>

        <?php
        echo Nav::widget([
            'options' => ['class' => 'nav nav-pills'],
            'encodeLabels' => false,
            'items' => [
                ['label' => 'Сегодня', 'url' => ['/manager', 'before' => $today, 'after' => $today]],
                ['label' => 'Вчера', 'url' => ['/manager', 'before' => $yesterday, 'after' => $yesterday]],
                ['label' => 'Все', 'url' => ['/manager', 'before' => null, 'after' => null]],
            ],
        ]);
        ?>

        <input type="text" class="form-control date-interval" placeholder="Задать интервал"/>

    </div>
</div>

<div class="filter__item">
    <span class="filter__item-title">Статус</span>
    <div class="text-subline"></div>
    <div class="filter__item-prop input-group-custom">
        <select name="status" class="form-control filter-status js-filter-status">
            <option value="-1">Все</option>
            <?php foreach (Order::$STATUS_TO_STRING as $k => $v) {
                if ($model->status == $k) {
                    echo "<option value='$k' selected>$v</option>";
                } else {
                    echo "<option value='$k'>$v</option>";
                }
            }?>
        </select>
    </div>
</div>

<div class="filter__item">
    <button type="submit" class="btn btn-success">Применить</button>
    <button class="btn js-print">
        <span class="lnr lnr-printer"></span>
        Печать
    </button>
</div>
<?php
ActiveForm::end();
?>


