<?php

namespace app\models\Good;

use Yii;
use yii\web\NotFoundHttpException;

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
    //  const KG_MEASURE = 42;
    /**
     * @inheritdoc
     */
    static $MEASURE_TO_STRING = [
        self::ITEM_MEASURE => 'Штука',
    ];

    public function getMeasureString() {
        return self::$MEASURE_TO_STRING[$this->measure];
    }

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
            ['measure', 'in', 'range' => [ self::ITEM_MEASURE ],
                'message' => 'Неизвестный тип единиц измерения.'],
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
            'measure' => 'Единица измерения',
            'c1id' => 'ГУИД 1С',
            'name' => 'Название',
            'pic' => 'Файл изображения',
            'price' => 'Цена',
            'category_id' => 'ID категории',
            'properties' => 'Свойства',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Menu::className(), ['id' => 'category_id']);
    }

    public static function findByIdOr404($id) {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Нет такого товара в каталоге.');
        }
    }

}
