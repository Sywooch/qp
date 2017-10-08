<?php
namespace app\commands;

use app\components\Html;
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
    private $_time;
    private $_formatted_time;

    public function initExcel($is_preorder, $pr) {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Бланк' . ($is_preorder ? ' предварительного' : '') . ' заказа поставщику №')
            ->setCellValue('B1', $this->_provider_order->id)

            ->setCellValue('A2', 'Дата')
            ->setCellValue('B2', PHPExcel_Shared_Date::PHPToExcel( $this->_time, true ))

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
                ->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel( $this->_time, true ));
        }


        $objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);

        return $objPHPExcel;
    }

    public function writeOrdersToExcel($orders, $objPHPExcel, $msg)
    {
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', $msg)
            ->setCellValue('B1', PHPExcel_Shared_Date::PHPToExcel( $this->_time, true ))
            ->getStyle("A1:B1")->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('B:B')
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


        $objPHPExcel->getActiveSheet()->getStyle('B1')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);


        $row = 2;
        foreach ($orders as $order) {
            $row++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$row", "Заказ №{$order->id}")
                ->setCellValue("B$row", "пользователь {$order->user->email}")
                ->setCellValue("C$row", "оплата №{$order->payment_id}")
                ->getStyle("A$row:C$row")->getFont()->setBold(true);
            $row++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$row", "Товар")
                ->setCellValue("B$row", "артикул")
                ->setCellValue("C$row", "количество")
                ->setCellValue("D$row", "цена за ед.")
                ->setCellValue("E$row", "поставщик")
                ->getStyle("A$row:E$row")->getFont()->setBold(true);
            $row++;
            foreach($order->getOrderProducts() as $op) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$row", $op->product_name)
                    ->setCellValue("B$row", $op->product_vendor)
                    ->setCellValue("C$row", $op->confirmed_count)
                    ->setCellValue("D$row", Html::unstyled_price($op->old_price))
                    ->setCellValue("E$row", PropertyValue::cachedFindOne($op->provider)->value)
                ;
                $row++;
            }
        }
    }

    public function excelLog()
    {
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)->setTitle("Отправленные заказы");
        $this->writeOrdersToExcel(
            Order::find()->where(['order.status' => Order::STATUS_PAID])->joinWith('user')->all(),
            $objPHPExcel,
            'Заказы отправленные поставщику от'
        );

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1)->setTitle("Не выданные заказы");
        $this->writeOrdersToExcel(
            Order::find()->where(['order.status' => [Order::STATUS_ORDERED, Order::STATUS_DELIVERED]])->joinWith('user')->all(),
            $objPHPExcel,
            'Заказы не выданные к'
        );
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($this->_dir_name . "/report.xlsx");

        Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name.' (отправлено роботом).'])
            ->setTo(Yii::$app->params['admin_email'])
            ->setSubject('Отчёт о заказах от  ' . $this->_formatted_time)
            ->setHtmlBody("Отчёт во вложении. На 1-ой странице не выданные заказы, на 2-ой заказанные.")
            ->attach($this->_dir_name . "/report.xlsx")
            ->send();
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

    public function selectOrders()
    {
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

    public function actionPreOrders()
    {
        foreach($this->selectPreorders() as $pr => $orders) {
            $this->_provider_order = new ProviderOrder([
                'pre_order_at' => $this->_time,
                'provider' => $pr,
                'pre_order_archive' => $this->_formatted_time . '.zip',
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

    public function actionOrders()
    {
        foreach($this->selectOrders() as $provider_order_id => $orders) {
            $this->_provider_order = ProviderOrder::cachedFindOne($provider_order_id);
            if(!$this->_provider_order) {
                throw new Exception("ERROR: Can't find pre-order with id $provider_order_id \n");
            }
            $this->_provider_order->order_at = $this->_time;
            $this->_provider_order->order_archive = $this->_formatted_time . '.zip';

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


    public function setUnpaidStatus()
    {
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

    public function setNotTakenStatus()
    {
        foreach(Order::find()->where(
            ['and',
                'status=' . Order::STATUS_DELIVERED,
                'updated_at<=' . ($this->_time - Yii::$app->params['order.deliveredExpire'])
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
            'updated_at<=' . ($this->_time - Yii::$app->params['order.deliveredExpire'])
        ]);
    }

    public function actionIndex()
    {
        $this->_time = time();
        $this->_formatted_time = date('Y-m-d_H_i_s', $this->_time);
        $this->_dir_name = \Yii::getAlias('@app') . '/temp/provider-order/' . $this->_formatted_time;
        is_dir($this->_dir_name) or mkdir($this->_dir_name, 0755, true);

        $this->excelLog();

        $this->setUnpaidStatus();
        $this->setNotTakenStatus();
        $this->actionPreOrders();
        $this->actionOrders();
        Helper::archiveDir($this->_dir_name);
        Helper::deleteDir($this->_dir_name);
    }
}
