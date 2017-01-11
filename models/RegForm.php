<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.12.16
 * Time: 15:19
 */

namespace app\models;
use yii\base\Model;
use Yii;

class RegForm extends Model
{
    public $email;
    public $name;
    public $password;
    public $repeat_password;
    public $pin;
    public $status;
    public function rules()
    {
        return [
            [['email'],'filter', 'filter' => 'trim'],
            [['email', 'password', 'repeat_password'],'required'],
            ['password', 'string', 'min' => 6, 'max' => 50],
            ['email', 'email'],
            ['email', 'notExistActivated'],
            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' =>[
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE
            ]],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'email' => 'Эл. почта',
            'name' => 'Имя',
            'password' => 'Пароль',
            'repeat_password' => 'Повторите пароль'
        ];
    }

    public function notExistActivated($attribute, $params) {
        if (!$this->hasErrors() && $user = User::findByEmail($this->email)) {
            if ($user->status !== User::STATUS_ACTIVE) {
                $user->delete();
            } else {
                $this->addError($attribute, 'Эта эл.почта уже занята');
            }
        }
    }

    public function reg()
    {
        $user = new User;
        $user->fill($this->email, $this->password);
        //return $user;
        return $user->save() ? $user : null;
    }

    public function sendActivationEmail($user)
    {
        $for_what = 'активации аккаунта';
        $link = Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/activate-account',
            'key' => $user->getPasswordResetToken(),
        ]);
        return Yii::$app->mailer->compose('linkEmail',
        [
            'for_what' => $for_what,
            'link' => $link,
        ])->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Ссылка для ' .$for_what . '  ' . Yii::$app->name)
            ->send();
    }
}
