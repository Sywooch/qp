<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "provider_order".
 *
 * @property integer $id
 * @property integer $pre_order_at
 * @property integer $order_at
 * @property string $provider
 */
class ProviderOrder extends CachedActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pre_order_at', 'order_at'], 'integer'],
            [['provider'], 'string', 'max' => 255],
        ];
    }
}
