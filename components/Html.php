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
     * @param $count integer
     * @return string
     */
    public static function counter($count = 1) {
        $html =
        '<div class="product-counter">'
            . '<button class="btn btn-down">-<button>'
            . '<input type="text" class="form-control" value="' . $count . '">'
            . '<button class="btn btn-up">+<button>'
        . '</div>';

        return $html;
    }
}