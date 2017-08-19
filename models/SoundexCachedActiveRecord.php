<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19.08.2017
 * Time: 19:26
 */

namespace app\models;

use app\components\Helper;

class SoundexCachedActiveRecord extends CachedActiveRecord
{
    static function soundex_columns()
    {
        return [];
    }

    public static function stringToSoundex($str, $for_like = false)
    {
        $str = Helper::ru2Lat($str);
        $words = preg_split(
            '/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/',
            $str,
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        $callback = $for_like ? function($x) {
            return '%' . soundex($x) . '%';
        } : "soundex";
        return implode(' ', array_map($callback, $words));

    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $soundex_val = '';
            foreach (static::soundex_columns() as $col) {
                $soundex_val .= ' ' . self::stringToSoundex($this->getAttribute($col));
            }
            $this->setAttribute('soundex_search', $soundex_val);
            return true;
        } else {
            return false;
        }
    }

    public static function  soundexSearch($query) {
        $query = self::stringToSoundex($query, true);
        return self::find()->where("soundex_search LIKE '$query'");
    }
}
