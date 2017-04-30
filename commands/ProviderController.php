<?php
namespace app\commands;

use app\models\Good\PropertyValue;
use app\models\Order;
use app\models\OrderProduct;
use app\models\ProviderOrder;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_NumberFormat;
use Yii;
use yii\console\Controller;
use app\components\Helper;

require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

class ProviderController extends Controller
{
    private $_dir_name;
    private $_provider_order;

    public function initExcel($is_preorder, $pr) {
        $time = time();
        if ($is_preorder) {
            $this->_provider_order = new ProviderOrder([
                'pre_order_at' => $time,
                'provider' => $pr
            ]);
        }
        else {
            $this->_provider_order = ProviderOrder::cachedFindOne([
                'order_at' => null,
                'provider' => $pr
            ]);
            if ($this->_provider_order ) {
                $this->_provider_order ->order_at = $time;
            }
            else {
                print("ERROR: Can't find pre-order for provider $pr\n");
                $this->_provider_order = new ProviderOrder([
                    'order_at' => $time,
                    'provider' => $pr
                ]);
            }
        }
        $this->_provider_order->save();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Бланк' . ($is_preorder ? ' предварительного' : '') . ' заказа поставщику №')
            ->setCellValue('B1', $this->_provider_order->id)

            ->setCellValue('A2', 'Дата')
            ->setCellValue('B2', PHPExcel_Shared_Date::PHPToExcel( $time ))

            ->setCellValue('C1', 'Название поставщика')
            ->setCellValue('D1', PropertyValue::cachedFindOne(['c1id' => $pr])->value)
            ->setCellValue('C2', 'ИД поставщика')
            ->setCellValue('D2', $pr)

            ->setCellValue('A5', '№')
            ->setCellValue('B5', 'Наименование')
            ->setCellValue('C5', 'Артикул')
            ->setCellValue('D5', 'Единица хранения')
            ->setCellValue('E5', 'Объём')
            ->setCellValue('F5', 'Цена')
            ->setCellValue('G5', 'Количество')
            ->setCellValue('H5', 'Стоимость');

        $objPHPExcel->getActiveSheet()->getStyle('C:C')
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('I:I')
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        if (!$is_preorder) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A3', 'к предварительному заказу №')
                ->setCellValue('B3', $this->_provider_order->id)
                ->setCellValue('A4', 'От')
                ->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel( $time ));
        }


        $objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

        return $objPHPExcel;
    }

    public function addOrders($orders, $excel, $is_preorder)
    {
        /** @var $excel PHPExcel */
        $excel_num = 6;
        foreach ($orders as $num => $order_product) {
            if ($is_preorder) {
                foreach (OrderProduct::find()->joinWith('order')->where([
                    'order.status' => Order::STATUS_NEW,
                    'order_product.product_vendor' => $order_product->product_vendor,
                ])->all() as $op) {
                    $op->provider_order_id = $this->_provider_order->id;
                    $op->save();
                }
            }
            /** @var $order_product OrderProduct */
            $excel_num = $num + 6;
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A' . $excel_num, $num + 1)
                ->setCellValue('B' . $excel_num, $order_product->product_name)
                // TODO: add this fields
                ->setCellValue('C' . $excel_num, $order_product->product_vendor)
//                ->setCellValue('D' . $excel_num, $product_name)
//                ->setCellValue('E' . $excel_num, $product_name)
                ->setCellValue('F' . $excel_num, $order_product->old_price / 100)
                ->setCellValue('G' . $excel_num, $order_product->count_by_c1id)
                ->setCellValue('H' . $excel_num, "=G$excel_num*F$excel_num");
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

        $order_products = OrderProduct::find()->joinWith('order')
            ->select(['provider, product_vendor, product_name, old_price, SUM(products_count) AS count_by_c1id'])
            ->where(['order.status' => $status_before])
            ->groupBy('provider, product_vendor, product_name, old_price')
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
            $provider_name = PropertyValue::cachedFindOne(['c1id' => $pr])->value;

            $excel = $this->initExcel($is_preorder, $pr);
            $this->addOrders($orders, $excel, $is_preorder);

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $dir_name = $this->_dir_name . Helper::ru2Lat("/$provider_name");

            is_dir($dir_name) or mkdir($dir_name);
            $objWriter->save($dir_name . "/$excel_name.xlsx");
        }

        foreach (Order::cachedFindAll(['status' => $status_before]) as $order) {
            $order->status = $status_after;
            $order->save();
        }
    }

    public function actionPreOrders() {
        $this->Excel(true);
    }

    public function actionOrders() {
        $this->Excel(false);
    }


    public function setUnpaidStatus() {
        Order::updateAll(['status' => Order::STATUS_UNPAID],
        ['or',
            ['status' => Order::STATUS_CONFIRMED],
            ['status' => Order::STATUS_PARTIAL_CONFIRMED],
        ]);
    }

    public function setNotTakenStatus() {
        Order::updateAll(['status' => Order::STATUS_NOT_TAKEN],['and',
            'status=' . Order::STATUS_DELIVERED,
            'updated_at<=' . (time() - Yii::$app->params['order.deliveredExpire'])
        ]);
        Order::flushCache();
    }

    public function actionIndex()
    {
        $this->_dir_name = 'temp/provider-order/' . date('Y-m-d');
        is_dir($this->_dir_name) or mkdir($this->_dir_name, 0755, true);
        $this->setUnpaidStatus();
        $this->setNotTakenStatus();
        $this->actionPreOrders();
        $this->actionOrders();
        Helper::archiveDir($this->_dir_name);
        Helper::deleteDir($this->_dir_name);

        $this->setUnpaidStatus();
    }
}
