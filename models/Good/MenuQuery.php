<?php

namespace app\models\Good;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use Yii;
use yii\caching\TagDependency;

class MenuQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Menu[]|array
     */
    public function all($db = null)
    {
        return Yii::$app->getDb()->cache(function ($db)
        {
            return parent::all($db);
        }, null, new TagDependency(['tags'=>'cache_table_' . Menu::tableName()]));
    }

    /**
     * @inheritdoc
     * @return Menu|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
