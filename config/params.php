<?php
$file = dirname(__FILE__).'/params.inc';
$content = file_get_contents($file);
$arr = unserialize($content);

return $arr + [
    'supportEmail' => 'no-reply@qpvl.ru',
    'user.passwordResetTokenExpire' => 3600,
    'phone.it' => '8 (423) 275-66-99',
    'order.deliveredExpire' => 14 * 24 * 3600,
    'public_host' => 'https://qpvl.ru/',
] + require(__DIR__ . '/private_params.php');
