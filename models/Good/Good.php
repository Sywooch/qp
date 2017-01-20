<?php

namespace app\models\Good;

use Yii;

/**
 * This is the model class for table "good".
 *
 * @property integer $id
 * @property integer $measure
 * @property string $c1id
 * @property string $name
 * @property string $pic
 * @property integer $price
 * @property integer $category_id
 * @property resource $properties
 *
 * @property Menu $category
 */
class Good extends \yii\db\ActiveRecord
{
    const ITEM_MEASURE = 796;
    const KG_MEASURE = NULL;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['measure', 'price', 'category_id'], 'integer'],
            [['properties'], 'string'],
            [['c1id', 'name', 'pic'], 'string', 'max' => 255],
            [['c1id'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'measure' => 'Measure',
            'c1id' => 'C1id',
            'name' => 'Name',
            'pic' => 'Pic',
            'price' => 'Price',
            'category_id' => 'Category ID',
            'properties' => 'Properties',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Menu::className(), ['id' => 'category_id']);
    }
}
