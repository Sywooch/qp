<?php

namespace app\models\Good;

use app\models\CachedSearchActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\caching\TagDependency;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 */
class Menu extends CachedSearchActiveRecord
{
    static function search_columns()
    {
        return 'name';
    }

    public function rules()
    {
        return [
            [[ 'name', 'c1id' ], 'string'],
            ['name', 'required'],
            ['c1id', 'unique'],
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

    public static function findByC1id($c1id) {
        return self::cachedFindOne([ 'c1id' => $c1id ]);
    }

    /**
     * @inheritdoc
     * @return return root node, or create new one if not yet exist.
     */
    public static function getRoot() {
        $root = self::getDb()->cache(function ($db)
        {
            return static::find()->roots()->one();
        }, null, new TagDependency(['tags'=>'cache_table_' . static::tableName()]));

        if ($root === null) {
            $root = new self([ 'name' => 'Категории товаров' ]);
            $root->makeRoot();
        }
        return $root;
    }

    public function getProducts() {
        return $this->hasMany(Good::className(), ['category_id' => 'id'])->where(['status' => Good::STATUS_OK]);
    }

    public function getProductCount() {
        if ($leaves = self::getDb()->cache(function ($db)
        {
            return $this->leaves()->all();
        }, null, new TagDependency(['tags'=>'cache_table_' . static::tableName()]))) {
            $ret = 0;
            foreach($leaves as $leaf) {
                $ret += self::getDb()->cache(function ($db) use($leaf)
                {
                    return $leaf->getProducts()->count();
                }, null, new TagDependency(['tags'=>'cache_table_' . Good::tableName()]));
            }
            return $ret;
        }
        return self::getDb()->cache(function ($db)
        {
            return $this->getProducts()->count();
        }, null, new TagDependency(['tags'=>'cache_table_' . Good::tableName()]));
    }
}
