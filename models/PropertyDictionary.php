<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "property_dictionary".
 *
 * @property integer $id
 * @property string $c1id
 * @property integer $property_id
 */
class PropertyDictionary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_dictionary';
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'c1id' => 'C1id',
            'property_id' => 'Property ID',
            'value' => 'Value',
        ];
    }
}
