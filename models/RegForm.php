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
    public $pin;
    public $status;
    public function rules()
    {
        return [
            [['email', 'name'],'filter', 'filter' => 'trim'],
            [['email', 'name'],'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],
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
            'name' => 'Имя'
        ];
    }

    public function reg()
    {
        $this->pin = $this->generatePin();

        $user = new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->status = $this->status;
        $user->setPassword($this->pin);
        $user->generateAuthKey();
        //return $user;
        return $user->save() ? $user : null;
    }

    public function generatePin() {
        return rand(1000, 9999);
    }

    public function sendActivationEmail($user)
    {
        return Yii::$app->mailer->compose('activationEmail', ['user' => $user, 'pin' => $this->pin])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo($this->email)
            ->setSubject('Активация для '.Yii::$app->name)
            ->send();
    }
}