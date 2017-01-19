<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use SimpleXMLElement;
use ZipArchive;
use app\models\Good\Menu;
use app\models\GoodProperty;
use app\models\PropertyDictionary;
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
                                    implode(', ', $modelc->getFirstErrors()));
                            }
                        }
                    }
                }
            }
        }
    }

    public function upload()
    {
        //Menu::findById(77)->appendTo(Menu::getRoot());
        //exit;
        if ($this->validate()) {
            $file_name = '../temp/' . date('d-m-Y_H-i-s', time()) . '.' . $this->zipFile->extension;
            $this->zipFile->saveAs($file_name);
//            $reader = new XMLReader();
//            $reader->open('zip://' . $file_name . '/a.xml');
//            var_dump($reader->read());
//            return true;

            $zip = new ZipArchive;
            $zip->open($file_name);

            $files = [];
            $file_pref = [
                'catalog' => 'webdata/000000001/import__',
                'property' => 'webdata/000000001/properties/1/import__',
                'good' => 'webdata/000000001/goods/1/import__',
                'price' => 'webdata/000000001/goods/1/prices__',
//                'image' => 'webdata/000000001/goods/import_files/',
            ];
            $n = $zip->numFiles;

            foreach ($file_pref as $file => $pref)  {
                for ($i = 0; $i < $n; $i++){
                    if (strpos($zip->getNameIndex($i), $pref) === 0) {
                        $files[$file] = $zip->getFromIndex($i);
                    }
                }
            }

            $this->catalogHandler(new SimpleXMLElement($files['catalog']));
            $this->propertyHandler(new SimpleXMLElement($files['property']));
            $zip->close();


        } else {
            return false;
        }
    }
}
?>