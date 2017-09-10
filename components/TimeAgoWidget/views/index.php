<?php
use app\components\Html;

/**
 * @var string $timeAgoText
 * @var string $date
 */
?>

<?=Html::tag('span', $timeAgoText, [
    'data-toggle' => 'tooltip',
    'title' => $date,
    'data-delay' => '{"show":"200"}'
]);
?>