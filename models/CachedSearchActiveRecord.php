<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19.08.2017
 * Time: 19:26
 */

namespace app\models;

use Eloquent\Lcs\LcsSolver;

function uniord($c) {
    if (ord($c{0}) >=0 && ord($c{0}) <= 127)
        return ord($c{0});
    if (ord($c{0}) >= 192 && ord($c{0}) <= 223)
        return (ord($c{0})-192)*64 + (ord($c{1})-128);
    if (ord($c{0}) >= 224 && ord($c{0}) <= 239)
        return (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
    if (ord($c{0}) >= 240 && ord($c{0}) <= 247)
        return (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
    if (ord($c{0}) >= 248 && ord($c{0}) <= 251)
        return (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
    if (ord($c{0}) >= 252 && ord($c{0}) <= 253)
        return (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
    if (ord($c{0}) >= 254 && ord($c{0}) <= 255)    //  error
        return FALSE;
    return 0;
}

function utf8SplitAndOrd($str, $len = 1)
{
    $arr = array();
    $strLen = mb_strlen($str, 'UTF-8');
    for ($i = 0; $i < $strLen; $i++)
    {
        $arr[] = uniord(mb_substr($str, $i, $len, 'UTF-8'));
    }
    return $arr;
}

class CachedSearchActiveRecord extends CachedActiveRecord
{
    static function search_column()
    {
        return 'name';
    }

    const THRESHOLD = 0.6;
    const MAX_LEN = 50;
    const MAX_COUNT = 100;
    const MATCH_MARK = 2;

    public static function search($query)
    {
        $all_data = static::cachedFindAll([]);
        $metric_data = [];
        $solver = new LcsSolver;
        $query = mb_strtolower($query);
        $query_array = utf8SplitAndOrd($query);
        $len = count($query_array);

        if ($len > self::MAX_LEN) {
            return [];
        }

        foreach ($all_data as $x) {
            $str = mb_strtolower($x->getAttribute(static::search_column()));
            if (mb_strpos($query, $str) !== false or  mb_strpos($str, $query) !== false){
                $metric_data[] = [self::MATCH_MARK, $x];
            }
            else {
                $str_array = utf8SplitAndOrd($str);
                $lcs = $solver->longestCommonSubsequence($str_array, $query_array);
                $val = count($lcs) / count($query_array);
                if ($val > self::THRESHOLD) {
                    $metric_data[] = [$val, $x];
                }
            }
        }
        usort($metric_data, function($x, $y) {
            return $x[0] > $y[0] ? -1 : 1;
        });

        $metric_data = array_slice($metric_data, 0, self::MAX_COUNT);
        return array_map(function($x) { return $x[1]; }, $metric_data);
    }
}
