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
        return $rub . $kop . ' <i class="fa fa-rub"></i>';
    }

    /**
     * Render input form for product counter
     *
     * @param $id integer product id
     * @param $count integer
     * @return string
     */
    public static function stepper($id, $count = 1) {
        $html = "<input type=\"number\" class=\"form-control\" value=\"$count\" data-product-id=\"$id\">";

        return $html;
    }
}