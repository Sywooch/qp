<?php

namespace app\models;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "order_product".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $product_c1id
 * @property integer $products_count
 *
 * @property Order $order
 */
class OrderProduct extends CachedActiveRecord
{
    // SUM(products_count) AS count_by_c1id, groupBy('product_c1id, product_name, old_price')
    public $count_by_c1id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_c1id'], 'required'],
            [['order_id', 'products_count', 'old_price', 'provider_order_id', 'confirmed_count'], 'integer'],
            [['product_vendor', 'provider'], 'string'],
            [['product_c1id', 'product_name'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(),
                'targetAttribute' => ['order_id' => 'id']],
            ['confirmed_count', 'default', 'value' => null ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_c1id' => '1с идентификатор товара',
            'products_count' => 'Заказанное количество товара',
            'confirmed_count' => 'Подтверждённое количество товара',
            'provider_order_id' => 'Номер заказа поставщику',
            'old_price' => 'Цена на момент заказа',
            'product_vendor' => 'Артикул',
            'provider' => '1с ИД Поставщика',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
