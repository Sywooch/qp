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
        $this->generateAuthKey();
        $user = new User;
        $user->name = "";
        $user->email = $this->email;
        $user->status = $this->status;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        //return $user;
        return $user->save() ? $user : null;
    }

    public function sendActivationEmail($user)
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user, 'pin' => $this->pin])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Активация для '.Yii::$app->name)
            ->send();
    }

    public function generateAuthKey()
    {
        $this->pin = Yii::$app->security->generateRandomString();
    }
}