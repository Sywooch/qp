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
class OrderProduct extends \yii\db\ActiveRecord
{
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
            [['order_id', 'products_count'], 'integer'],
            [['product_c1id'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public static function findByIdOr404($id) {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Нет такой позиции в заказах.');
        }
    }
}
