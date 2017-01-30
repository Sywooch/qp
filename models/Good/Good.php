<?php

namespace app\models\Good;

use yii\web\NotFoundHttpException;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;
use baibaratsky\yii\behaviors\model\SerializedAttributes;

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
class Good extends \yii\db\ActiveRecord implements CartPositionInterface
{

    use CartPositionTrait;

    public function behaviors()
    {
        return [
            'serializedAttributes' => [
                'class' => SerializedAttributes::className(),
                // Define the attributes you want to be serialized
                'attributes' => ['properties'],
            ],
        ];
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getId()
    {
        return $this->id;
    }
    

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
            ['properties', 'checkIsArrayOrEmpty'],
            [['c1id', 'name', 'pic'], 'string', 'max' => 255],
            [['c1id'], 'unique'],
            ['measure', 'in', 'range' => [ self::ITEM_MEASURE ],
                'message' => 'Неизвестный тип единиц измерения.'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(),
                'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function checkIsArrayOrEmpty($attribute, $params)
    {
        if ($this->properties && !is_array($this->properties)) {
            $this->addError('config', 'Properties should be array');
        }
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
            'price' => 'Цена в копейках',
            'category_id' => 'ID категории',
            'properties' => 'Свойства',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Menu::className(), ['id' => 'category_id']);
    }

    public function getImgPath()
    {
        return 'img/catalog/good/' . ($this->pic ? $this->pic : 'default.png');
    }

    public static function findByIdOr404($id) {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Нет такого товара в каталоге.');
        }
    }

}
