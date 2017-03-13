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
            [['order_id', 'products_count', 'old_price'], 'integer'],
            [['product_c1id', 'product_name'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(),
                'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getProvider()
    {
        return $this->count_by_c1id == 1 ? 'QQQQ' : 'EEEE';     //TODO: $this->provider;
    }
}
