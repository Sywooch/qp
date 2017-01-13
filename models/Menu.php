<?php

namespace app\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\base\InvalidParamException;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 */
class Menu extends \yii\db\ActiveRecord
{
    public function rules()
    {
        return [
        ['name', 'string'],
        ['name', 'required'],
    ];
}
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                // 'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }
    /**
     * @inheritdoc
     * @return MenuQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * @inheritdoc
     * @return return root node, or create new one if not yet exist.
     */
    public static function findById($id) {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            throw new InvalidParamException('Нет такого раздела в каталоге.');
        }
    }

    public static function getRoot() {
        if (($root = Menu::find()->roots()->one()) === null) {
            $root = new Menu([ 'name' => 'Категории товаров' ]);
            $root->makeRoot();
        }
        return $root;
    }
}
