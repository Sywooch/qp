<?php
namespace app\components;

use Yii;

class View extends \yii\web\View
{
    /**
     * @param $price integer
     * @return string
     */
    public function convertPrice($price)
    {
        $rub = (int)($price / 100) . ' руб. ';
        $kop = ($price % 100) ? $price % 100 . 'коп.' : '';
        return $rub . $kop;
    }
}