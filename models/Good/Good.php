<?php

namespace app\models\Good;

use app\models\CachedActiveRecord;
use Yii;
use yz\shoppingcart\CartPositionProviderInterface;
use baibaratsky\yii\behaviors\model\SerializedAttributes;
use app\models\Bookmark;
use himiklab\yii2\search\behaviors\SearchBehavior;

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

class Good extends CachedActiveRecord implements CartPositionProviderInterface
{
    public $offset;
    public function getCartPosition($params = [])
    {
        return \Yii::createObject([
            'class' => ProductCartPosition::className(),
            'id' => $this->id,
        ]);
    }

    public function behaviors()
    {
        return [
            'search' => [
                'class' => SearchBehavior::className(),
                'searchScope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->select(['id', 'name', 'properties', 'pic', 'price']);
                    //$model->andWhere(['indexed' => true]);
                },
                'searchFields' => function ($model) {
                    /** @var self $model */
                    $prop = [];
                    foreach ($model->properties as $k => $v) {
                        $prop[GoodProperty::cachedFindOne($k)->name] = PropertyValue::cachedFindOne($v)->value;
                    }

                    return [
                        ['name' => 'id', 'value' => $model->id, 'type' => SearchBehavior::FIELD_UNINDEXED],
                        ['name' => 'name', 'value' => $model->name],
                        ['name' => 'properties', 'value' => serialize($prop)],
                        ['name' => 'pic', 'value' => $model->pic, SearchBehavior::FIELD_BINARY],
                        ['name' => 'price', 'value' => $model->price],
                    ];
                }
            ],

            'serializedAttributes' => [
                'class' => SerializedAttributes::className(),
                // Define the attributes you want to be serialized
                'attributes' => ['properties'],
            ],
        ];
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->pic) {
                unlink($this->getImgPath());
            }
            return true;
        } else {
            return false;
        }
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

    const ORDERING_PRICE_ACS = 1;
    const ORDERING_PRICE_DESC = 2;
    const ORDERING_NAME = 3;
    const ORDERING_BOOKMARK = 4;

    static $ORDERING_TO_STRING = [
        self::ORDERING_PRICE_ACS    => 'По возрастанию цены',
        self::ORDERING_PRICE_DESC   => 'По убованию цены',
        self::ORDERING_NAME         => 'По наименованию',
//        self::ORDERING_BOOKMARK     => 'По рейтингу',
    ];

    public function getMeasureString() {
        return self::$MEASURE_TO_STRING[$this->measure];
    }

    const STATUS_ERROR = 1;
    const STATUS_HIDDEN = 2;
    const STATUS_OK = 10;

    static $STATUS_TO_STRING = [
        self::STATUS_ERROR      => 'Ошибка',
        self::STATUS_HIDDEN     => 'Скрытый',
        self::STATUS_OK         => 'ОК',
    ];


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
            [['measure', 'price', 'category_id', 'status'], 'integer'],
            ['properties', 'checkIsArrayOrEmpty'],
            [['c1id', 'name', 'pic', 'vendor', 'provider'], 'string', 'max' => 255],
            [['c1id', 'vendor'], 'unique'],
            [['vendor', 'provider'], 'required'],
            ['measure', 'in', 'range' => array_keys(self::$MEASURE_TO_STRING),
                'message' => 'Неизвестный тип единиц измерения.'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(),
                'targetAttribute' => ['category_id' => 'id']],
            ['status', 'in', 'range' => array_keys(self::$STATUS_TO_STRING)],
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
            'provider' => '1с ИД Поставщика',
            'vendor' => 'Артикул',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Menu::className(), ['id' => 'category_id']);
    }

    public function getBookmark()
    {
        return $this->hasOne(Bookmark::className(), ['product_id' => 'id'])
            ->onCondition(['user_id' => \Yii::$app->user->getId()]);
    }

    public function getBookmarksCount()
    {
        return Bookmark::cachedGetCount(['product_id' => $this->id]);
    }

    public function getImgPath()
    {
        return $this->pic ? 'img/catalog/good/'. $this->pic : 'img/null.svg';
    }

    static public function findOkStatus($cond)
    {
        $ret = self::findOneOr404($cond);
        if ($ret->status == self::STATUS_OK) {
            return $ret;
        }
        else {
            Yii::$app->session->setFlash('error', "Товар $ret->name недоступен");
            return false;
        }
    }
}
