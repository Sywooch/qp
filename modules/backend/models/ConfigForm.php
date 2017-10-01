<?php
namespace app\modules\backend\models;

use yii\base\Model;

class ConfigForm extends Model
{
    public $admin_email;
    public $working_time;
    public $weekend_working_time;
    public $shop;
    public $phone;
    public $address;
    public $law_address;
    public $inn;
    public $ogrn;
    public $bank;
    public $ks;
    public $bik;
    public $rs;

    public function attributeLabels()
    {
        return [
            'admin_email' => 'Эл. ящик администратора',
            'working_time' => 'Время работы в будни',
            'weekend_working_time' => 'Время работы в СБ и ВС',
            'shop' => 'Название магазина',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'law_address' => 'Юридический адрес',
            'inn' => 'ИНН/КПП',
            'ogrn' => 'ОГРН',
            'bank' => 'Наименование Банка',
            'ks' => 'К/с',
            'bik' => 'Бик',
            'rs' => 'Р/с',
        ];
    }

    public function rules()
    {
        $attr = array_keys($this->attributeLabels());
        return [
            [$attr, 'string'],
            [$attr, 'default', 'value' => ''],
        ];
    }
}
?>
