<?php

namespace app\models;
use yii\base\Model;

/**
* ContactForm is the model for feedback table.
*
 * @property string $before
 * @property string $after
 * @property integer $status
*
*/
class OrderFilterForm extends Model
{
    public $before, $after, $status;
    public function rules()
    {
        return [
            [['before', 'after'], 'string'],
            ['status', 'in', 'range' => array_keys(Order::$STATUS_TO_STRING)],
            [['before', 'after', 'status'], 'safe'],
        ];
    }
}