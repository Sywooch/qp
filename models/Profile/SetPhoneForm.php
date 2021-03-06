<?php

namespace app\models\Profile;
use yii\base\Model;
use Yii;

class SetPhoneForm extends Model
{
    public $phone;
    public function rules()
    {
        return [
            [['phone'], 'required'],
            ['phone', 'match',
                'pattern' => '/\+7 [0-9]{3} [0-9]{3}-[0-9]{2}-[0-9]{2}/',
                'message' => 'Необходимо ввести номер телефона.',
            ]
        ];
    }
    public function attributeLabels()
    {
        return [
            'phone' => 'Номер телефона',
        ];
    }
    public function setPhone($phone)
    {
        $user = Yii::$app->user->identity;
        $user->setPhone($phone);

        Yii::$app->session->setFlash('error', 'key: ' . $user->phone_validation_key);   // sendValidationSms dummy

        return $this->sendValidationSms() and $user->save();
    }

    public function sendValidationSms() {
//        sendValidationSms dummy;
        return true;
    }
}
