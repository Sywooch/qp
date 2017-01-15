<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\web\UploadedFile;
use SimpleXMLElement;
use ZipArchive;
use app\models\Menu;
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

    public function recursivelyAddCategory($xml, $menu) {
        if ($xml->Группы->Группа) {
            foreach ($xml->Группы->Группа as $xmlc) {
                $c1idc = (string) $xmlc->Ид;
                $menuc = Menu::findByC1id($c1idc);

                $is_deletec = (string) $xmlc->ПометкаУдаления === 'true' ? true : false;
                if ($is_deletec) {
                    if (!$menuc) continue;
                    if ($menuc->deleteWithChildren()) {
                        Yii::$app->session->addFlash('warning', "Категория <i>$menuc->name</i> и все вложенные в неё удалены");
                    }
                    else {
                        Yii::$app->error("Возникла ошибка при удалении категории <i>$menuc->name</i>");
                    }
                    return;
                }

                $was_new = false;
                if (!$menuc) {
                    $was_new = true;
                    $menuc = new Menu([
                        'name' => (string)$xmlc->Наименование,
                        'c1id' => $c1idc,
                    ]);
                };

                if (!$menuc->parents(1)->one() || ($menuc->parents(1)->one()->id != $menu->id)) {
                    if (!$menuc->appendTo($menu)) {
                        Yii::$app->error("Возникла ошибка при добавлении категории <i>$menuc->name</i> ");
                    } else {
                        if ($was_new) {
                            Yii::$app->session->addFlash('success', "Категория <i>$menuc->name </i> добавлена");
                        }
                        else {
                            Yii::$app->session->addFlash('warning', "Категория <i>$menuc->name </i> перенесена");
                        }
                    }
                }
                $this->recursivelyAddCategory($xmlc, $menuc);
            }
        }
    }

    public function catalogHandler($xml_data) {
        $xml = new SimpleXMLElement($xml_data);
        $root = Menu::getRoot();


//        var_dump($xml->Классификатор->Группы->Группа->Группы->Группа[0]->Группы->Группа[0]->Группы->Группа);
//        exit;
        $this->recursivelyAddCategory($xml->Классификатор->Группы->Группа, Menu::getRoot());
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

            $this->catalogHandler($files['catalog']);
            $zip->close();


        } else {
            return false;
        }
    }
}
?>