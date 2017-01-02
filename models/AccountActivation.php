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

    public function activateAccount()
    {
        /* @var $_user User */
        $user = $this->_user;
        $user->status = User::STATUS_ACTIVE;
        $user->removePasswordResetToken();
        if ($user->save()) {
            $auth = Yii::$app->authManager;
            $auth->assign($auth->getRole('user'), $user->getId());

            return true;
        }
        return false;
    }
}
