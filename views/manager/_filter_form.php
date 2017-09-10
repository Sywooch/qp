<?php
/**
 * @var $model app\models\OrderFilterForm
 */
use app\models\Order;
use kartik\date\DatePicker;
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
    <div class="input-group-custom">
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
    <div class="input-group-custom">
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


        echo '<label class="control-label">Задать интервал</label>';
        <div class="date-picker">
            <?php
            echo DatePicker::widget([
                'name' => 'after',
                'value' => $model->after,
                'type' => DatePicker::TYPE_RANGE,
                'name2' => 'before',
                'value2' => $model->before,
                'separator' => ' - ',
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]);
            ?>
            <span class="js-clear after">
                <span class="lnr lnr-cross"></span>
            </span>
            <span class="js-clear before">
                <span class="lnr lnr-cross"></span>
            </span>
        </div>

    </div>
</div>

<div class="filter__item">
    <span class="filter__item-title">Статус</span>
    <div class="text-subline"></div>
    <div class="input-group-custom">
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
    <button class="btn js-print" type="button">
        <span class="lnr lnr-printer"></span>
        Печать
    </button>
</div>
<?php
ActiveForm::end();
?>


