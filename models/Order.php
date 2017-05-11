<?php

namespace app\models;

use app\components\Html;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer public_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property OrderProduct[] $orderProducts
 */
class Order extends CachedActiveRecord
{
    const STATUS_NEW = 10;
    const STATUS_PROVIDER_CHECKING = 15;
    const STATUS_CONFIRMED = 20;
    const STATUS_PARTIAL_CONFIRMED = 21;
    const STATUS_UNCONFIRMED = 22;
    // Orders between this states have secret key
    const STATUS_PAID = 30;
    const STATUS_ORDERED = 35;
    const STATUS_DELIVERED = 40;
    // Orders between this states have secret key
    const STATUS_DONE = 50;
    const STATUS_UNPAID = 51;
    const STATUS_CANCELED = 52;
    const STATUS_NOT_TAKEN = 53;

    static $STATUS_TO_STRING = [
        self::STATUS_NEW                => 'Проверяется наличие',
        self::STATUS_PROVIDER_CHECKING  => 'Проверяется наличие (Отправлен поставщику)',
        self::STATUS_CONFIRMED          => 'Подтверждён',
        self::STATUS_PARTIAL_CONFIRMED  => 'Частично подтверждён',
        self::STATUS_UNCONFIRMED        => 'Полностью не подтверждён',
        self::STATUS_PAID               => 'Оплачен',
        self::STATUS_ORDERED            => 'Оплачен (Отправлен поставщику)',
        self::STATUS_DELIVERED          => 'Готов к выдаче',
        self::STATUS_DONE               => 'Выполнен',
        self::STATUS_UNPAID             => 'Оплата просрочена',
        self::STATUS_CANCELED           => 'Отменён',
        self::STATUS_NOT_TAKEN          => 'Не забран с пункта выдачи',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['public_id', 'password'], 'string', 'max' => 255],
            [['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']],
            ['status', 'in', 'range' => array_keys(self::$STATUS_TO_STRING)],
            ['status', 'default', 'value' => self::STATUS_NEW],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function attributeLabels()
    {
        return [
            'public_id' => 'Номер заказа',
            'user_id' => 'ID покупателя',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',
            'status' => 'Статус',
            'status_str' => 'Статус',
            'password' => 'Секретный ключ',
            'totalPrice' => 'Сумма заказа',
            'confirmedPrice' => 'Сумма к оплате',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */

    public function getStatus_str() {
        return self::$STATUS_TO_STRING[$this->status];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public function getOrderProducts()
    {
        return OrderProduct::cachedFindAll(['order_id' => $this->id]);
    }

    public function getConfirmedPrice()
    {
        $has_nonconf = false;
        $ret = Html::unstyled_price(array_reduce($this->orderProducts, function($carry, $item) use(&$has_nonconf){
            if (!isset($item->confirmed_count)) {
                $has_nonconf = true;
            }
            return $carry + $item->confirmed_count * $item->old_price;
        }));
        return $has_nonconf ? null : $ret;
    }

    public function getTotalPrice()
    {
        return Html::unstyled_price(array_reduce($this->orderProducts, function($carry, $item) {
                return $carry + $item->products_count * $item->old_price;
            }));
    }

    public function generatePassword() {
        $this->password = $this->public_id . '-' . sprintf("%04d", rand(1,9999));
    }

    public function pay() {
        // TODO: implement rest stuff
        $this->status = Order::STATUS_PAID;
        return $this->save();
    }

    public function canPaid() {
        return
            $this->status == Order::STATUS_CONFIRMED ||
            $this->status == Order::STATUS_PARTIAL_CONFIRMED;
    }

    public function cancel() {
        // TODO: implement rest stuff
        $this->status = Order::STATUS_CANCELED;
        return $this->save();
    }

    public function canCanceled() {
        return Order::STATUS_NEW <= $this->status  && $this->status <=  Order::STATUS_PARTIAL_CONFIRMED;
    }

    public function haveSecretKey() {
        return $this->status == self::STATUS_DELIVERED;
    }

    public function getLink() {
        $link = Yii::$app->urlManager->createAbsoluteUrl(
            [
                "/profile/order/view/",
                'id' => $this->id,
            ]);
        return "<a href=$link>Заказ $this->public_id</a>";
    }
}
