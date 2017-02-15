<?php

namespace app\models\Good;

use app\models\CachedActiveRecord;
use Yii;

/**
 * This is the model class for table "good_property".
 *
 * @property integer $id
 * @property string $c1id
 * @property string $name
 * @property integer $type
 */


class GoodProperty extends CachedActiveRecord
{
    const STRING_TYPE = 0;
    const DICTIONARY_TYPE = 10;
    const NUMBER_TYPE = 20;
    const DATE_TYPE = 30;

    static private $__type_enum = [
        'Строка' => self::STRING_TYPE,
        'Справочник' => self::DICTIONARY_TYPE,
        'Число' => self::NUMBER_TYPE,
    ];

    static function getTypeByC1name($c1name)
    {
        if (!array_key_exists($c1name, static::$__type_enum)) {
            Yii::$app->session->addFlash('warning',
                "Нейзвестный тип свойства <i>$c1name</i>, будет использован тип <i>Строка</i>"
            );
            return self::STRING_TYPE;
        }
        return static::$__type_enum[$c1name];
    }
    /*
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'good_property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type'], 'integer'],
            ['type', 'in', 'range' => array_values(static::$__type_enum)],
            [['c1id', 'name'], 'string', 'max' => 255],
            [['c1id'], 'unique'],
        ];
    }

    public function valueId($value) {
        if (!$value) {
            return null;
        }

        if ($this->type == self::DICTIONARY_TYPE) {             // ADD code for rest cases
            if ($value_model = PropertyValue::FindOne(['c1id' => $value])) {
                return $value_model->id;
            }
            else {
                Yii::$app->session->addFlash('error',
                    "Неизвестное значение свойства товара <i>$this->name</i> 
                        с ГУИД <i>$value</i>");
            }
        }
        else {
            $value_model = PropertyValue::FindOne(['value' => $value, 'property_id' => $this->id]);
            if (!$value_model) {
                $value_model = new PropertyValue([
                    'value' => $value,
                    'property_id' => $this->id,
                ]);
                if (!$value_model->save()) {
                    Yii::$app->session->addFlash('error',
                        "Ошибка при добавлении значения <i>$value->value</i> в справочник. " .
                        implode(', ', $value_model->getFirstErrors()));
                }
            }
            return $value_model->id;
        }
    }
}
