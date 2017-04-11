<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;

/**
 * ContactForm is the model for feedback table.
 *
 * @property string $body
 * @property string $email
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $rating
 *
 */
class ContactForm extends CachedActiveRecord
{
    public $verifyCode;

    const MAX_BODY_SIZE = 10000;

    const STATUS_UNMODERATED = 0;
    const STATUS_VISIBLE = 10;
    const STATUS_INVISIBLE = 20;

    const SCENARIO_USER_FEEDBACK = 0;

    static $STATUS_TO_STRING = [
        self::STATUS_UNMODERATED => 'Непроверенный',
        self::STATUS_VISIBLE => 'Отображаемый',
        self::STATUS_INVISIBLE => 'Скрытый',
    ];

    static $STATUS_TO_LABEL = [
        self::STATUS_UNMODERATED => 'label label-info',
        self::STATUS_VISIBLE => 'label label-success',
        self::STATUS_INVISIBLE => 'label label-warning',
    ];

    public function getStatusString() {
        return self::$STATUS_TO_STRING[$this->status];
    }

    public function getStatusLabel() {
        return self::$STATUS_TO_LABEL[$this->status];
    }

    static $RATING_TO_STRING = [
        'Отрицательный', 'Нейтральный', 'Положительный'
    ];

    public function getRatingString() {
        return self::$RATING_TO_STRING[$this->rating];
    }

    public static function tableName() {
        return 'feedback';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email', 'name', 'body'], 'required'],
            [['body'], 'string', 'max' => self::MAX_BODY_SIZE],
            [['name'], 'string', 'max' => 25],
            [['rating'], 'integer'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'on' => self::SCENARIO_USER_FEEDBACK ],
            ['status', 'default', 'value' => self::STATUS_UNMODERATED],
            ['rating', 'default', 'value' => 1],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Я не робот (Клик, чтобы изменить)',
            'body' => 'Сообщение',
            'name' => 'Ваше имя',
            'rating' => 'Оценка',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate() && Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['adminEmail'])
                ->setFrom([Yii::$app->params['supportEmail']])
                ->setSubject('Обращение на сайте ' . Yii::$app->name)
                ->setTextBody("Пользователь $this->name с эл. ящиком $this->email оставил следующее обращение на сайте " .
                    Yii::$app->name . ":\n" . $this->body)
                ->send()
            ) {

            return true;
        }
        return false;
    }
}
