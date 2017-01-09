<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

class AccountActivation extends Model
{
    private $_user;

    public function __construct($key, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым.');
        $this->_user = User::findByPasswordResetToken($key);
        if(!$this->_user)
            throw new InvalidParamException('Неверный ключ.');
        parent::__construct($config);
    }

    /* @var $_user User */
    public function getUser() {
        return $this->_user;
    }
}
