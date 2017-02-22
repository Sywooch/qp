<?php

namespace app\models\Profile;
use yii\base\Model;

class SetPasswordForm extends Model
{
    public $password, $repeat_password;
    public function rules()
    {
        return [
            [['repeat_password', 'password'], 'required'],
            ['password', 'string', 'min' => 6, 'max' => 50],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'password' => 'Новый пароль',
            'repeat_password' => 'Повторите новый пароль',
        ];
    }
}
