<?php
/**
 * @var $this yii\web\View
 * @var $for_what app\models\User
 * @var $link string-link
 */

use yii\helpers\Html;

echo "Ссылка для $for_what аккаунта" . Yii::$app->name . ': ';
echo $link;
