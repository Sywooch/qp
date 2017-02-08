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
        switch ($date[1]){
            case 1: $m='января'; break;
            case 2: $m='февраля'; break;
            case 3: $m='марта'; break;
            case 4: $m='апреля'; break;
            case 5: $m='мая'; break;
            case 6: $m='июня'; break;
            case 7: $m='июля'; break;
            case 8: $m='августа'; break;
            case 9: $m='сентября'; break;
            case 10: $m='октября'; break;
            case 11: $m='ноября'; break;
            case 12: $m='декабря'; break;
        }
        return (int)$date[0].' '.$m;
    }
}