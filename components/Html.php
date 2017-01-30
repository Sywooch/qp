<?php
namespace app\components;

use Yii;

class Html extends \yii\helpers\Html
{
    public static function price($price) {
        $rub = (int)($price / 100);
        $kop = ($price % 100) ? '.' . $price % 100 : '';
        return $rub . $kop . ' <i class="fa fa-rub"></i>';
    }
}