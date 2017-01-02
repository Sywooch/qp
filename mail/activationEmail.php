<?php
/**
 * @var $this yii\web\View
 * @var $user app\models\User
 * @var $pin app\models\RegForm
 */

use yii\helpers\Html;
echo 'Привет '.Html::encode($user->name).'.';
echo 'Ссылка для активация аккаунта' . Yii::$app->name . ': ';
echo Yii::$app->urlManager->createAbsoluteUrl(
    [
        '/site/activate-account',
        'key' => $pin
    ]
);
