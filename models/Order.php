<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

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
            ['password', 'filter', 'filter' => 'trim'],
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['public_id', 'password'], 'string'],
            [['user_id'], 'exist',
                'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']],
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
            'public_id' => 'ID',
            'user_id' => 'ID покупателя',
            'created_at' => 'Создан',
            'updated_at' => 'Изменён',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    public function getTotalPrice()
    {
        return array_reduce($this->orderProducts, function($carry, $item) {
            return $carry + $item->products_count * $item->old_price;
        });
    }

    public function generatePassword() {
        $this->password = sprintf("%04d", rand(1,9999));
    }

    public function checkPassword($password) {
        return false; // TODO: $password === $this->password;
    }
}
