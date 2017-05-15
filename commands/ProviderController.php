<?php
namespace app\commands;

use app\models\Good\PropertyValue;
use app\models\Order;
use app\models\OrderProduct;
use app\models\Profile\Message;
use app\models\ProviderOrder;
use Exception;
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
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Бланк' . ($is_preorder ? ' предварительного' : '') . ' заказа поставщику №')
            ->setCellValue('B1', $this->_provider_order->id)

            ->setCellValue('A2', 'Дата')
            ->setCellValue('B2', PHPExcel_Shared_Date::PHPToExcel( $time, true ))

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

    public function selectPreorders() {
        $order_products = OrderProduct::find()->joinWith('order')
            ->select(["provider, product_vendor, product_name, old_price, SUM(products_count) AS count_by_c1id"])
            ->where(['order.status' => Order::STATUS_NEW])
            ->groupBy('provider, product_vendor, product_name, old_price')
            ->all();
        $providers = [];
        foreach ($order_products as $op) {
            $key = $op->provider;
            if (array_key_exists($key, $providers)) {
                $providers[$key][] = $op;
            }
            else {
                $providers[$key] =  [$op];
            }
        }
        return $providers;
    }

    public function selectOrders() {
        $order_products = OrderProduct::find()->joinWith('order')
            ->select(["provider_order_id, provider, product_vendor, product_name, old_price, SUM(confirmed_count) AS count_by_c1id"])
            ->where(['order.status' => Order::STATUS_PAID])
            ->groupBy('provider_order_id, provider, product_vendor, product_name, old_price')
            ->all();
        $provider_orders = [];
        foreach ($order_products as $op) {
            $key = $op->provider_order_id;
            if (array_key_exists($key, $provider_orders)) {
                $provider_orders[$key][] = $op;
            }
            else {
                $provider_orders[$key] = [$op];
            }
        }
        return $provider_orders;
    }

    public function actionPreOrders() {
        foreach($this->selectPreorders() as $pr => $orders) {
            $this->_provider_order = new ProviderOrder([
                'pre_order_at' => time(),
                'provider' => $pr
            ]);
            if(!$this->_provider_order->save()) {
                throw new Exception("ERROR: while saving provider_order in DB: " .
                    implode(', ', $this->_provider_order->getFirstErrors()) . "\n");
            }

            $excel = $this->initExcel(true, $pr);
            $this->addOrders($orders, $excel, true);

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $dir_name = $this->_dir_name . Helper::ru2Lat('/' . PropertyValue::cachedFindOne(['c1id' => $pr])->value);

            is_dir($dir_name) or mkdir($dir_name);
            $objWriter->save($dir_name . '/' . $this->_provider_order->id . '-preorder.xlsx');
        }
        Order::updateAll(['status' => Order::STATUS_PROVIDER_CHECKING], ['status' => Order::STATUS_NEW]);
    }

    public function actionOrders() {
        foreach($this->selectOrders() as $provider_order_id => $orders) {
            $this->_provider_order = ProviderOrder::cachedFindOne($provider_order_id);
            if(!$this->_provider_order) {
                throw new Exception("ERROR: Can't find pre-order with id $provider_order_id \n");
            }
            $this->_provider_order->order_at = time();
            if(!$this->_provider_order->save()) {
                throw new Exception("ERROR: while saving provider_order in DB: " .
                    implode(', ', $this->_provider_order->getFirstErrors()) . "\n");
            }
            $provider_c1id = $this->_provider_order->provider;
            $excel = $this->initExcel(false, $provider_c1id);
            $this->addOrders($orders, $excel, false);

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $dir_name = $this->_dir_name . Helper::ru2Lat('/' .
                    PropertyValue::cachedFindOne(['c1id' => $provider_c1id])->value);

            is_dir($dir_name) or mkdir($dir_name);
            $objWriter->save($dir_name . '/' . $this->_provider_order->id . '-order.xlsx');
        }
        Order::updateAll(['status' => Order::STATUS_ORDERED], ['status' => Order::STATUS_PAID]);
    }


    public function setUnpaidStatus() {
        foreach(Order::find()->where(
        ['or',
            ['status' => Order::STATUS_CONFIRMED],
            ['status' => Order::STATUS_PARTIAL_CONFIRMED],
        ])->all() as $order) {
            $msg = new Message([
                'user_id' => $order->user_id,
                // TODO: оплачен до...
                'text' => "Оплата $order->link была просрочена.",
            ]);
            $msg->save();
        }

        Order::updateAll(['status' => Order::STATUS_UNPAID],
        ['or',
            ['status' => Order::STATUS_CONFIRMED],
            ['status' => Order::STATUS_PARTIAL_CONFIRMED],
        ]);
    }

    public function setNotTakenStatus() {
        foreach(Order::find()->where(
            ['and',
                'status=' . Order::STATUS_DELIVERED,
                'updated_at<=' . (time() - Yii::$app->params['order.deliveredExpire'])
            ])->all() as $order) {
            $msg = new Message([
                'user_id' => $order->user_id,
                // TODO: оплачен до...
                'text' => "Вы не забрали $order->link с пункта выдачи в срок.",
            ]);
            $msg->save();
        }

        Order::updateAll(['status' => Order::STATUS_NOT_TAKEN],['and',
            'status=' . Order::STATUS_DELIVERED,
            'updated_at<=' . (time() - Yii::$app->params['order.deliveredExpire'])
        ]);
    }

    public function actionIndex()
    {
        $this->_dir_name = \Yii::getAlias('@app') . '/temp/provider-order/' . date('Y-m-d');
        is_dir($this->_dir_name) or mkdir($this->_dir_name, 0755, true);
        $this->setUnpaidStatus();
        $this->setNotTakenStatus();
        $this->actionPreOrders();
        $this->actionOrders();
        Helper::archiveDir($this->_dir_name);
        Helper::deleteDir($this->_dir_name);
    }
}
