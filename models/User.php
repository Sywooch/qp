<?php

namespace app\models;


use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use \nodge\eauth\ErrorException;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $role
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $role;

//    const STATUS_DELETED = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 10;

    static $STATUS_TO_STRING = [
//        self::STATUS_DELETED => 'Удалён',
        self::STATUS_NOT_ACTIVE  => 'Не активирован',
        self::STATUS_ACTIVE => 'Активирован',
    ];

    public function rules()
    {
        return [
            [['email', 'name'], 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['status', 'default', 'value' => User::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::$STATUS_TO_STRING)],
            ['role', 'in', 'range' => array_keys(Yii::$app->authManager->getRoles())],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    public static function getRolesNames() {
        return array_keys(\Yii::$app->authManager->getRoles());
    }

    public function getStatusString() {
        return self::$STATUS_TO_STRING[$this->status];
    }
    public function getRole() {
        if ($roles = Yii::$app->authManager->getRolesByUser($this->getId())) {
            return array_keys($roles)[0];
        }
    }

    public static function tableName()
    {
        return 'user';
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'status' => 'Статус',
            'role' => 'Роль',
            'role' => 'Роль',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->role) {
            $auth = Yii::$app->authManager;
            if ($auth->getRolesByUser($this->getId())) {
                $auth->revokeAll($this->getId());
            }
            if (!$auth->assign($auth->getRole($this->role), $this->getId())) {
                return false;
            }
        }
        return parent::save($runValidation, $attributeNames);
    }

    /////////////////////////////////////////////
    // FINDERS
    /////////////////////////////////////////////

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

///////////////// EAuth staff ////////////////////
    /**
     * @var array EAuth attributes
     */

    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param \nodge\eauth\ServiceBase $service
     * @return User
     * @throws ErrorException
     */
    public static function findByEAuth($service) {
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }
        if ($email = $service->getAttribute('email')) {
            if ($user = static::findByEmail($email)) {
                return $user;
            }
            else {
                $user = new self;
                $pass = Yii::$app->security->generateRandomString(6);
                $user->fill($email, $pass);
                if (!$user->save() || !$user->ActivateAccount()) {
                    throw new ErrorException('Ошибка при регистрации пользователя через соц. сеть.');
                }
                $link = Html::a("Личный кабинет&rarr;Настройки профиля", ['/profile/edit']);
                Yii::$app->session->setFlash('warning',
                    "Для вашего аккаунта был сгенерирован пароль: <code>$pass</code>,
                    чтобы изменить его перейдите в $link.");

                return static::findByEmail($email);
            }
        }
        else {
            throw new ErrorException('Сервис авторизации не вернул эл. почту.');
        }
    }
//////////////////////////////////////////////////////

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            Yii::$app->session->setFlash('error', 'Срок действия ключа истёк');
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            // 'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /////////////////////////////////////////////
    // HELPERS
    /////////////////////////////////////////////

    public function activateAccount()
    {
        $this->status = User::STATUS_ACTIVE;
        $this->removePasswordResetToken();
        if ($this->save()) {
            $auth = Yii::$app->authManager;
            $auth->assign($auth->getRole('user'), $this->getId());
            return true;
        }
        return false;
    }

    public function fill($email, $password) {
        $this->setPassword($password);
        $this->email = $email;
        $this->name = 'qwe';
        $this->generatePasswordResetToken();
        $this->status = self::STATUS_NOT_ACTIVE;
    }

    public function getPasswordResetToken()
    {
        return $this->password_reset_token;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getPhone()
    {
        if (!$this->phone_validation_key) {
            return $this->phone;
        }
        return null;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        $this->generatePhoneKey();
    }

    public function generatePhoneKey()
    {
        $this->phone_validation_key = sprintf("%04d", rand(1,9999));
    }

    public function validatePhoneKey($key)
    {
        if ($this->phone_validation_key == $key) {
            $this->phone_validation_key = null;
            if (!$this->save()) {
                Yii::error('Произошла ошибка при подтверждении номера телефона');
            }
            return true;
        }
        return false;
    }
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function findByIdOr404($id) {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Пользователя с таким id не существует.');
        }
    }

    public function getBookmarks() {
        return $this->hasMany(Bookmark::className(), ['user_id' => 'id']);
    }

}
