<?php

namespace app\models\Good;
use app\models\CachedActiveRecord;

use Yii;

/**
 * This is the model class for table "property_value".
 *
 * @property integer $id
 * @property string $c1id
 * @property string $value
 * @property integer $property_id
 */
class PropertyValue extends CachedActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id'], 'integer'],
            [['property_id', 'value'], 'required'],
            [['c1id', 'value'], 'string', 'max' => 255],
            [['c1id'], 'unique'],
        ];
    }

}
