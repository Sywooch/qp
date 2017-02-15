<?php
namespace app\components;

use Yii;

class Html extends \yii\helpers\Html
{
    /**
     * Convert price from kopeiki(int) to rubles(float)
     *
     * @param $price integer
     * @return string
     */
    public static function price($price) {
        $rub = (int)($price / 100);
        $kop = ($price % 100) ? '.' . $price % 100 : '';
        return '<span>'. $rub . $kop . '</span> <i class="fa fa-rub"></i>';
    }

    /**
     * Convert price from kopeiki(int) to rubles(float)
     *
     * @param $price integer
     * @return float
     */
    public static function rubles($price) {
        return round($price / 100);
    }

    /**
     * Render input form for product counter
     *
     * @param $id integer product id
     * @param $count integer
     * @return string
     */
    public static function stepper($id, $count = 1) {
        $html = "<input type=\"number\" class=\"form-control product-count\" value=\"$count\" data-product-id=\"$id\">";

        return $html;
    }

    public static function dateRu($date) {
        $date=explode(" ", $date);
        $m = ['января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря'];
        return (int)$date[0].' '.$m[(int)$date[1] - 1];
    }
}