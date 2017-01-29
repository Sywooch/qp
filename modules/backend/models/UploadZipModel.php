<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use SimpleXMLElement;
use ZipArchive;
use app\models\Good\Menu;
use app\models\Good\GoodProperty;
use app\models\Good\PropertyDictionary;
use app\models\Good\Good;
use Yii;

class UploadZipModel extends Model
{
    /**
     * @var UploadedFile
     */
    public $zipFile;

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
                        Yii::$app->session->addFlash('warning',
                            "Категория <i>$menuc->name</i> и все вложенные в неё удалены");
                    }
                    else {
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
                                Yii::$app->session->addFlash('success', "Категория <i>$menuc->name </i> добавлена");
                            }
                            else {
                                Yii::$app->session->addFlash('warning', "Категория <i>$menuc->name </i> перенесена");
                            }
                            $this->recursivelyAddCategory($xmlc, $menuc);
                        }
                        else {
                            Yii::$app->session->addFlash('error',
                                "Возникла ошибка при добавлении категории <i>$menuc->name</i>. " .
                                implode(', ', $menuc->getFirstErrors()));
                        }
                    }
                }
            }
        }
    }

    public function catalogHandler($xml) {
        $this->recursivelyAddCategory($xml->Классификатор->Группы->Группа, Menu::getRoot());
    }

    public function propertyHandler($xml) {
        foreach($xml->Классификатор->Свойства->Свойство as $prop_xml) {
            $prop_c1id = (string) $prop_xml->Ид;
            $prop_model = GoodProperty::findOne([ 'c1id' => $prop_c1id ]);

            if (static::str2bool($prop_xml->ПометкаУдаления)) {
                if ($prop_model && !$prop_model->delete()) {
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
                        Yii::$app->session->addFlash('error',
                            "Ошибка при добавлении свойства товара <i>$prop_model->name</i>. " .
                            implode(', ', $prop_model->getFirstErrors()));
                        continue;
                    }
                }

                if ($prop_model->type === GoodProperty::DICTIONARY_TYPE) {
                    foreach($prop_xml->ВариантыЗначений->Справочник as $dict_xml) {
                        $dict_c1id = (string) $dict_xml->ИдЗначения;
                        if (!PropertyDictionary::findOne([ 'c1id' => $dict_c1id ])) {
                            $dict_model = new PropertyDictionary([
                                'c1id' => $dict_c1id,
                                'value' => (string) $dict_xml->Значение,
                                'property_id' => $prop_model->id,
                            ]);
                            if (!$dict_model->validate() || !$dict_model->save()) {
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
                    Yii::$app->session->addFlash('error',
                        "Ошибка при удалении товара <i>$good_model->name</i>");
                };
            }
            else {
                if (!$category = Menu::findByC1id($good_xml->Группы->Ид)) {
                    Yii::$app->session->addFlash('error',
                        "Неизвестная категория товаров с ГУИД <i>$good_xml->Группы->Ид</i>");
                    continue;
                }

                $props = [];
                foreach ($good_xml->ЗначенияСвойств->ЗначенияСвойства as $prop_val_xml) {
                    if (!$prop = GoodProperty::findOne([ 'c1id' => (string) $prop_val_xml->Ид ])) {
                        Yii::$app->session->addFlash('error',
                            "Неизвестное свойство товара с ГУИД <i>$prop_val_xml->Ид</i>");
                        continue;
                    }
                    $val = $prop->valueToString((string) $prop_val_xml->Значение);
                    if (isset($val)) {
                        $props[$prop->name] = [ 'value' => $val, 'type' => $prop->type ];
                    }
                }
                if (!$good_model) {
                    $good_model = new Good();
                }
                $good_model->setAttributes([
                    'c1id' => $good_c1id,
                    'name' => (string) $good_xml->Наименование,
                    'measure' => (int) $good_xml->БазоваяЕдиница,

                    // I made this gavno because i can't copy dir with copy()
                    // and ZipArchive::extractTo extract with full path inside archive

                    'pic' => (string) $good_xml->Картинка ?
                        'webdata/000000001/goods/1/' . (string) $good_xml->Картинка : '',
                    'category_id' => $category->id,
                    'properties' => $props,
                ]);
                if (!$good_model->validate() || !$good_model->save()) {
                    Yii::$app->session->addFlash('error',
                        "Ошибка при добавлении товара <i>$good_model->name</i>. " .
                        implode(', ', $good_model->getFirstErrors()));
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
                if (!$good_model->validate() || !$good_model->save()) {
                    Yii::$app->session->addFlash('error',
                        "Ошибка при добавлении цены товара <i>$good_model->name</i>. " .
                        implode(', ', $good_model->getFirstErrors()));
                };
            }
            else {
                Yii::$app->session->addFlash('error',
                    "Неизвествный товар с ГУИД <i>$good_c1id</i>.");
            }
        }
    }


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
                'good' => 'webdata/000000001/goods/1/import__',
                'price' => 'webdata/000000001/goods/1/prices__',
            ];
            $zip_img_dir = 'webdata/000000001/goods/1/import_files/';
            $server_img_dir = 'img/catalog/good/';

            $n = $zip->numFiles;
            for ($i = 0; $i < $n; $i++){
                $fname = $zip->getNameIndex($i);
                if (strpos($fname, $zip_img_dir) === 0 && pathinfo($fname, PATHINFO_EXTENSION)) {

                    if ($zip->extractTo($server_img_dir, $fname) !== true) {
                        Yii::$app->session->addFlash('error',
                            "Не удалось извлечь изображение <i>$fname</i>.");
                    }
                }
            }

            $files = [];
            foreach ($file_pref as $name => $pref)  {
                for ($i = 0; $i < $n; $i++){
                    $fname = $zip->getNameIndex($i);
                    if (strpos($fname, $pref) === 0) {
                        $files[$name] = $zip->getFromIndex($i);
                    }
                }
            }

            $this->catalogHandler(new SimpleXMLElement($files['catalog']));
            $this->propertyHandler(new SimpleXMLElement($files['property']));
            $this->goodHandler(new SimpleXMLElement($files['good']));
            $this->priceHandler(new SimpleXMLElement($files['price']));

            $zip->close();
        } else {
            return false;
        }
    }
}
?>