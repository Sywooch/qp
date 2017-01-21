<?php

namespace app\models\Profile;
use yii\base\Model;

use Yii;

class ResetPasswordForm extends Model
{
    public $email;
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    public function sendEmail($user)
    {
        $for_what = 'сброса пароля';
        $user->generatePasswordResetToken();
        if (!$user->save()) {
            return null;
        }
        $link = Yii::$app->urlManager->createAbsoluteUrl(
            [
                '/profile/edit/password',
                'key' => $user->getPasswordResetToken(),
            ]);

        return Yii::$app->mailer->compose('linkEmail',
        [
            'for_what' => $for_what,
            'link' => $link,
        ])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Ссылка для ' . $for_what . '  ' . Yii::$app->name)
            ->send();
    }
}
