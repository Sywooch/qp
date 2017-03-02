<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\caching\TagDependency;
use yii\web\NotFoundHttpException;

/**
 * Class ActiveRecord
 * @package common\my\yii2
 */
class CachedActiveRecord extends ActiveRecord
{
    protected function flushCache()
    {
        TagDependency::invalidate(Yii::$app->cache,
            new TagDependency(['tags' => 'cache_table_' . static::tableName()]));
        return true;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->flushCache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->flushCache();
            return true;
        } else {
            return false;
        }
    }

    public static function cachedFindOne($cond) {
        return static::getDb()->cache(function ($db) use($cond)
        {
            return static::findOne($cond);
        }, null, new TagDependency(['tags'=>'cache_table_' . static::tableName()]));
    }

    public static function cachedGetCount($cond = []) {
        return static::getDb()->cache(function ($db) use($cond)
        {
            return static::find()->where($cond)->count();
        }, null, new TagDependency(['tags'=>'cache_table_' . static::tableName()]));
    }

    public static function cachedFindAll($cond = []) {
        return static::getDb()->cache(function ($db) use($cond)
        {
            return static::find()->where($cond)->all();
        }, null, new TagDependency(['tags'=>'cache_table_' . static::tableName()]));
    }

    public static function findOneOr404($cond) {
        if (($model = self::cachedFindOne($cond)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Не существует такого " . static::tableName());
        }
    }
}