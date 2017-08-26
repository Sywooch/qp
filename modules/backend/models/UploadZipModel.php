<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use SimpleXMLElement;
use ZipArchive;
use app\models\Good\Menu;
use app\models\Good\GoodProperty;
use app\models\Good\PropertyValue;
use app\models\Good\Good;
use Yii;

class UploadZipModel extends Model
{
    /**
     * @var $zipFile UploadedFile
     */
    public $zipFile;
    private $_report;
    const VERBOSE = 0;

    public function rules()
    {
        return [
            [['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
        ];
    }

    public static function str2bool($str) {
        return (string)$str === 'true';
    }

    public function recursivelyAddCategory($xml, $menu) {
        if ($xml->Группы->Группа) {
            foreach ($xml->Группы->Группа as $xmlc) {
                $c1idc = (string) $xmlc->Ид;
                $menuc = Menu::findByC1id($c1idc);

                if (static::str2bool($xmlc->ПометкаУдаления) && $menuc) {
                    if ($menuc->deleteWithChildren()) {
                        $this->_report['warning']['удалений корневых категорий']++;
                        self::VERBOSE && Yii::$app->session->addFlash('warning',
                            "Категория <i>$menuc->name</i> и все вложенные в неё удалены");
                    }
                    else {
                        $this->_report['error']['ошибок']++;
                        Yii::$app->session->addFlash('error',
                            "Возникла ошибка при удалении категории <i>$menuc->name</i>");
                    }
                }
                else {
                    $was_new = false;
                    if (!$menuc) {
                        $was_new = true;
                        $menuc = new Menu([
                            'name' => (string)$xmlc->Наименование,
                            'c1id' => $c1idc,
                        ]);
                    };

                    if (!$menuc->parents(1)->one() || ($menuc->parents(1)->one()->id != $menu->id)) {
                        if ($menuc->validate() && $menuc->appendTo($menu)) {
                            if ($was_new) {
                                $this->_report['success']['добавлений категорий']++;
                                self::VERBOSE && Yii::$app->session->addFlash('success', "Категория <i>$menuc->name </i> добавлена");
                            }
                            else {
                                $this->_report['warning']['перемещений категорий']++;
                                self::VERBOSE && Yii::$app->session->addFlash('warning', "Категория <i>$menuc->name </i> перенесена");
                            }
                        }
                        else {
                            $this->_report['error']['ошибок']++;
                            Yii::$app->session->addFlash('error',
                                "Возникла ошибка при добавлении категории <i>$menuc->name</i>. " .
                                implode(', ', $menuc->getFirstErrors()));
                            continue;
                        }
                    }
                    $this->recursivelyAddCategory($xmlc, $menuc);
                }
            }
        }
    }

    public function catalogHandler($xml) {
        $this->recursivelyAddCategory($xml->Классификатор, Menu::getRoot());
    }

    public function propertyHandler($xml) {
        foreach($xml->Классификатор->Свойства->Свойство as $prop_xml) {
            $prop_c1id = (string) $prop_xml->Ид;
            $prop_model = GoodProperty::findOne([ 'c1id' => $prop_c1id ]);

            if (static::str2bool($prop_xml->ПометкаУдаления)) {
                if ($prop_model && !$prop_model->delete()) {
                    $this->_report['error']['ошибок']++;
                    Yii::$app->session->addFlash('error',
                        "Ошибка при удалении свойства товара <i>$prop_model->name</i>");
                };
            }
            else {
                if (!$prop_model) {
                    $prop_model = new GoodProperty([
                        'c1id' => $prop_c1id,
                        'name' => (string)$prop_xml->Наименование,
                        'type' => GoodProperty::getTypeByC1name((string)$prop_xml->ТипЗначений),
                    ]);
                    if (!$prop_model->validate() || !$prop_model->save()) {
                        $this->_report['error']['ошибок']++;
                        Yii::$app->session->addFlash('error',
                            "Ошибка при добавлении свойства товара <i>$prop_model->name</i>. " .
                            implode(', ', $prop_model->getFirstErrors()));
                        continue;
                    }
                }

                if ($prop_model->type === GoodProperty::DICTIONARY_TYPE) {
                    foreach($prop_xml->ВариантыЗначений->Справочник as $dict_xml) {
                        $dict_c1id = (string) $dict_xml->ИдЗначения;
                        if (!PropertyValue::findOne([ 'c1id' => $dict_c1id ])) {
                            $dict_model = new PropertyValue([
                                'c1id' => $dict_c1id,
                                'value' => (string) $dict_xml->Значение,
                                'property_id' => $prop_model->id,
                            ]);
                            if (!$dict_model->validate() || !$dict_model->save()) {
                                $this->_report['error']['ошибок']++;
                                Yii::$app->session->addFlash('error',
                                    "Ошибка при добавлении значения <i>$dict_model->value</i> в справочник. " .
                                    implode(', ', $dict_model->getFirstErrors()));
                            }
                        }
                    }
                }
            }
        }
    }

    public function goodHandler($xml) {
        foreach($xml->Каталог->Товары->Товар as $good_xml) {

            $good_c1id = (string) $good_xml->Ид;
            $good_model = Good::findOne([ 'c1id' => $good_c1id ]);

            if (static::str2bool($good_xml->ПометкаУдаления)) {
                if ($good_model && !$good_model->delete()) {
                    $this->_report['error']['ошибок']++;
                    Yii::$app->session->addFlash('error',
                        "Ошибка при удалении товара <i>$good_model->name</i>");
                };
            }
            else {
                if (!$category = Menu::findByC1id($c1id = (string)$good_xml->Группы->Ид)) {
                    $this->_report['error']['ошибок']++;
                    Yii::$app->session->addFlash('error',
                        "Неизвестная категория товаров с ГУИД <i>$c1id</i>");
                    continue;
                }


                if (!$good_model) {
                    $good_model = new Good();
                }

                $props = [];
                foreach ($good_xml->ЗначенияСвойств->ЗначенияСвойства as $prop_val_xml) {
                    if (!$prop = GoodProperty::cachedFindOne([ 'c1id' => (string) $prop_val_xml->Ид ])) {
                        $this->_report['error']['ошибок']++;
                        Yii::$app->session->addFlash('error',
                            "Неизвестное свойство товара с ГУИД <i>$prop_val_xml->Ид</i>");
                        continue;
                    }
                    $str_val = (string) $prop_val_xml->Значение;
                    $val_id = $prop->valueId($str_val);
                    if (isset($val_id)) {
                        $props[$prop->id] = $val_id;
                    }
                    if ($prop->name == 'Поставщик') {
                        $good_model->provider = $str_val;
                    }
                    if ($prop->name == 'Акционный товар') {
                        $good_model->is_discount = $str_val == 'true';
                    }
                }
                $good_model->setAttributes([
                    'c1id' => $good_c1id,
                    'name' => (string) $good_xml->Наименование,
                    'measure' => (int) $good_xml->БазоваяЕдиница,
                    'vendor' => (string) $good_xml->Артикул,
                    'properties' => $props,
                    // I made this gavno because i can't copy dir with copy()
                    // and ZipArchive::extractTo extract with full path inside archive

                    'pic' => (string) $good_xml->Картинка ?
                        'webdata/000000001/goods/1/' . (string) $good_xml->Картинка : '',
                    'category_id' => $category->id,
                ]);

                $is_new = $good_model->isNewRecord;
                if (!$good_model->validate() || !$good_model->save()) {
                    $this->_report['error']['ошибок']++;
                    Yii::$app->session->addFlash('error',
                        "Ошибка при добавлении товара <i>$good_model->name</i>. " .
                        implode(', ', $good_model->getFirstErrors()));
                }
                else {
                    $this->_report['success'][$is_new ? 'добавлений товаров' : 'изменений товаров']++;
                    self::VERBOSE && Yii::$app->session->addFlash('success',
                        "Товар <i>$good_model->name</i> " . ($is_new ? 'добавлен.' : 'обновлён.'));
                }
            }
        }
    }

    public function priceHandler($xml) {
        foreach($xml->ПакетПредложений->Предложения->Предложение as $price_xml) {
            $good_c1id = (string) $price_xml->Ид;
            if ($good_model = Good::findOne([ 'c1id' => $good_c1id ])) {
                    // Change ЦенаЗаЕдиницу for another measure type
                $good_model->price = (int) (100 * floatval($price_xml->Цены->Цена->ЦенаЗаЕдиницу));
                if ($good_model->validate() and $good_model->save()) {
                    if ($good_model->status == Good::STATUS_NEW) {
                        $good_model->status = Good::STATUS_OK;
                    }
                }
                if (!$good_model->validate() || !$good_model->save()) {
                    $this->_report['error']['ошибок']++;
                    Yii::$app->session->addFlash('error',
                        "Ошибка при добавлении цены товара <i>$good_model->name</i>. " .
                        implode(', ', $good_model->getFirstErrors()));
                };
            }
            else {
                $this->_report['error']['ошибок']++;
                Yii::$app->session->addFlash('error',
                    "Попытка добавить цену неизвествного товар с ГУИД <i>$good_c1id</i>.");
            }
        }
    }

    /**
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            $file_name = '../temp/' . date('d-m-Y_H-i-s', time()) . '.' . $this->zipFile->extension;
            $this->zipFile->saveAs($file_name);
//            $reader = new XMLReader();
//            $reader->open('zip://' . $file_name . '/a.xml');
//            var_dump($reader->read());
//            return true;

            $zip = new ZipArchive;
            $zip->open($file_name);

            $file_pref = [
                'catalog' => 'webdata/000000001/import__',
                'property' => 'webdata/000000001/properties/1/import__',
            ];
            $zip_img_dir = 'webdata/000000001/goods/1/import_files/';
            $server_img_dir = 'img/catalog/good/';

            $n = $zip->numFiles;
            for ($i = 0; $i < $n; $i++){
                $fname = $zip->getNameIndex($i);
                if (strpos($fname, $zip_img_dir) === 0 && pathinfo($fname, PATHINFO_EXTENSION)) {

                    if ($zip->extractTo($server_img_dir, $fname) !== true) {
                        $this->_report['error']['ошибок']++;
                        Yii::$app->session->addFlash('error',
                            "Не удалось извлечь изображение <i>$fname</i>.");
                    }
                }
            }

            $files = [];
            for ($i = 0; $i < $n; $i++){
                $fname = $zip->getNameIndex($i);
                if (count($parts = explode('webdata/000000001/goods/', $fname)) == 2) {
                    if (count($parts = explode('/', $parts[1])) == 2) {
                        if (strpos($parts[1], 'prices') === 0) {
                            $files['goods'][$parts[0]]['prices'] = $zip->getFromIndex($i);
                        }
                        elseif (strpos($parts[1], 'import') === 0) {
                            $files['goods'][$parts[0]]['goods'] = $zip->getFromIndex($i);
                        }
                    }
                }
                else {
                    foreach ($file_pref as $name => $pref)  {
                        if (strpos($fname, $pref) === 0) {
                            $files[$name] = $zip->getFromIndex($i);
                        break;
                        }
                    }
                }
            }

            ini_set('memory_limit', '110M');
            ini_set('max_execution_time', 1000);
            $this->_report = [
                'success' => [
                    'добавлений категорий' => 0,
                    'добавлений товаров' => 0,
                    'изменений товаров' => 0,
                ],
                'warning' => [
                    'удалений корневых категорий' => 0,
                    'перемещений категорий' => 0,
                ],
                'error' => [
                    'ошибок' => 0,
                ]

            ];

            $this->catalogHandler(new SimpleXMLElement($files['catalog']));
            $this->propertyHandler(new SimpleXMLElement($files['property']));
            foreach($files['goods'] as $good) {
                $this->goodHandler(new SimpleXMLElement($good['goods']));
                $this->priceHandler(new SimpleXMLElement($good['prices']));
            }

            $this->_report['error']['добавлений товаров с ошибкой'] =
                Good::updateAll(['status' => Good::STATUS_ERROR], ['status' => Good::STATUS_NEW]);

            $this->_report['success']['добавлений товаров'] -= $this->_report['error']['добавлений товаров с ошибкой'];

            $zip->close();
            foreach ($this->_report as $swe => $subjs) {
                foreach ($subjs as $subj => $count) {
                    if ($count) {
                        Yii::$app->session->addFlash($swe, "Произошло $count $subj");
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
?>