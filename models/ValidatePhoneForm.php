<?php

namespace app\models;
use yii\base\Model;
use Yii;

class ValidatePhoneForm extends Model
{
    public $key;
    public function rules()
    {
        return [
            [['key'],'required'],
            ['key', 'validateKey']
        ];
    }
    public function attributeLabels()
    {
        return [
            'key' => 'Код подтверждения',
        ];
    }
    public function validateKey($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = Yii::$app->user->identity;
            if (!$user || !$user->validatePhoneKey($this->key)) {
                $this->addError($attribute, 'Неправильный код подтверждения');
            }
        }
    }
}
