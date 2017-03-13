<?php
namespace app\commands;

use app\models\Order;
use app\models\OrderProduct;
use app\models\ProviderOrder;
use Faker\Provider\cs_CZ\DateTime;
use MongoDB\BSON\Timestamp;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_NumberFormat;
use RecursiveIteratorIterator;
use yii\console\Controller;
use ZipArchive;

require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

class ProviderController extends Controller
{
    private $_dir_name;

    public function initExcel($is_preorder, $pr) {
        $time = time();
        if ($is_preorder) {
            $provider_order = new ProviderOrder([
                'pre_order_at' => $time,
                'provider' => $pr
            ]);
        }
        else {
            $provider_order = ProviderOrder::cachedFindOne([
                'order_at' => null,
                'provider' => $pr
            ]);
            $provider_order->order_at = $time;
        }
        $provider_order->save();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C1', 'Бланк' . ($is_preorder ? 'предварительного' : '') . ' заказа поставщику №')
            ->setCellValue('D1', $provider_order->id)

            ->setCellValue('C2', 'Дата')
            ->setCellValue('D2', PHPExcel_Shared_Date::PHPToExcel( $time ))

            ->setCellValue('A5', '№')
            ->setCellValue('B5', 'Наименование')
            ->setCellValue('C5', 'Артикул')
            ->setCellValue('D5', 'Единица хранения')
            ->setCellValue('E5', 'Объём')
            ->setCellValue('F5', 'Цена')
            ->setCellValue('G5', 'Количество')
            ->setCellValue('H5', 'Стоимость')
            ->setCellValue('I5', 'Поставщик');

        if (!$is_preorder) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('C3', 'к предварительному заказу №')
                ->setCellValue('D3', $provider_order->id)
                ->setCellValue('C4', 'От')
                ->setCellValue('D4', PHPExcel_Shared_Date::PHPToExcel( $time ));
        }


        $objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
        $objPHPExcel->getActiveSheet()->getStyle('D4')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

        return $objPHPExcel;
    }

    public function addOrders($orders, $excel, $provider)
    {
        /** @var $excel PHPExcel */
        foreach ($orders as $num => $order) {
            $excel_num = $num + 6;
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A' . $excel_num, $num + 1)
                ->setCellValue('B' . $excel_num, $order->product_name)
                // TODO: add this field
//                ->setCellValue('C' . $excel_num, $product_name)
//                ->setCellValue('D' . $excel_num, $product_name)
//                ->setCellValue('E' . $excel_num, $product_name)
                ->setCellValue('F' . $excel_num, $order->old_price / 100)
                ->setCellValue('G' . $excel_num, $order->count_by_c1id)
                ->setCellValue('H' . $excel_num, "=G$excel_num*F$excel_num")
                ->setCellValue('I' . $excel_num, $provider);
        }
        $excel->setActiveSheetIndex(0)
            ->setCellValue('B' . ($excel_num + 1), "Итого")
            ->setCellValue('G' . ($excel_num + 1), "=SUM(G6:G$excel_num)")
            ->setCellValue('H' . ($excel_num + 1), "=SUM(H6:H$excel_num)")
        ;
    }

    public function Excel($is_preorder) {
        list($status_before, $status_after, $excel_name) = $is_preorder ?
            [Order::STATUS_NEW, Order::STATUS_PROVIDER_CHECKING, 'preorder'] :
            [Order::STATUS_PAID, Order::STATUS_ORDERED, 'order'];

        foreach (Order::cachedFindAll(['status' => $status_before]) as $order) {
            $order->status = $status_after;
            $order->save();
        }

        $order_products = OrderProduct::find()->joinWith('order')
            ->select(['product_c1id, product_name, old_price, SUM(products_count) AS count_by_c1id'])
            ->where(['order.status' => $status_after])
            ->groupBy('product_c1id, product_name, old_price')
            ->all();

        $providers = [];
        foreach ($order_products as $op) {
            if (array_key_exists($op->provider, $providers)) {
                $providers[$op->provider][] = $op;
            }
            else {
                $providers[$op->provider] =  [$op];
            }
        }

        foreach($providers as $pr => $orders) {
            $excel = $this->initExcel($is_preorder, $pr);
            $this->addOrders($orders, $excel, $pr);

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $dir_name = $this->_dir_name . "/$pr";
            is_dir($dir_name) or mkdir($dir_name);
            $objWriter->save($dir_name . "/$excel_name.xlsx");
        }
    }

    public function actionPreOrders() {
        $this->Excel(true);
    }

    public function actionOrders() {
        $this->Excel(false);
    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function archiveDay() {
        // Get real path for our folder
        $rootPath = realpath($this->_dir_name);

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open("$this->_dir_name.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);

                // Add current file to "delete list"
                // delete it later cause ZipArchive create archive only after calling close function and ZipArchive lock files until archive created)
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }

    public function setUnpaidStatus() {
    }

    public function actionIndex()
    {
        $this->_dir_name = 'web/provider-order/' . date('Y-m-d');
        is_dir($this->_dir_name) or mkdir($this->_dir_name, 0777, true);
        
        $this->actionPreOrders();
        $this->actionOrders();
        $this->archiveDay();
        self::deleteDir($this->_dir_name);

        $this->setUnpaidStatus();
    }
}
