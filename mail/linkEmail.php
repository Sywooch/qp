<?php
/**
 * @var $this yii\web\View
 * @var $for_what app\models\User
 * @var $link string-link
 * @var $end string
 */

echo "<a href=$link>Ссылка</a> для $for_what аккаунта " . Yii::$app->name . '.' . (isset($end) && $end ? $end : '');
