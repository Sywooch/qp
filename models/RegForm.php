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
    public $pin;
    public $status;
    public function rules()
    {
        return [
            [['email'],'filter', 'filter' => 'trim'],
            [['email', 'password'],'required'],
            ['password', 'string', 'min' => 6, 'max' => 50],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => User::className(),
                'message' => 'Эта почта уже занята.'],
            ['status', 'default', 'value' => User::STATUS_ACTIVE, 'on' => 'default'],
            ['status', 'in', 'range' =>[
                User::STATUS_NOT_ACTIVE,
                User::STATUS_ACTIVE
            ]],
            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE, 'on' => 'emailActivation'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'email' => 'Эл. почта',
            'name' => 'Имя',
            'password' => 'Пароль',
        ];
    }

    public function reg()
    {
        $user = new User;
        $user->fill($this->email, $this->password);
        $user->name = "";           // remove that
        //return $user;
        return $user->save() ? $user : null;
    }

    public function sendActivationEmail($user)
    {
        return Yii::$app->mailer->compose('activationEmail',
                ['user' => $user, 'pin' => $user->getPasswordResetToken()]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Активация для '.Yii::$app->name)
            ->send();
    }

}
