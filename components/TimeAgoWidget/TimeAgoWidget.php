<?php

namespace app\components\TimeAgoWidget;

use app\components\Html;
use DateInterval;
use DateTime;
use yii\bootstrap\Widget;

class TimeAgoWidget extends Widget
{
    /**
     * @var DateTime $datetime
     */
    public $datetime;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('index', [
            'timeAgoText' => $this->passed_time(date('Y-m-d H:i:s', $this->datetime)),
            'date' => date(" H:i:s d.m.Y", $this->datetime)
        ]);
    }

    function passed_time($date_str) {
        $time = time();
        $date_str = strtotime($date_str);
        $tm = date('H:i', $date_str);
        $d = date('d', $date_str);
        $m = date('m', $date_str);
        $y = date('Y', $date_str);
        $last = round(($time - $date_str)/60);
        $m_name = Html::getMounts(true);
        $m_name = $m_name[(int)$m - 1];
        $d_name = (int) $d;
        if ($last < 1) return "Только что";
        if( $last < 55 ) return $last. " " . Html::ending($last, ['минуту', 'минуты', 'минут']) . " назад";
        elseif($d.$m.$y == date('dmY',$time)) return "Сегодня в $tm";
        elseif($d.$m.$y == date('dmY', strtotime('-1 day'))) return "Вчера в $tm";
        elseif($y == date('Y',$time)) return "$d_name $m_name в $tm ";
        else return "$tm $d_name $m_name $y";
    }
}
