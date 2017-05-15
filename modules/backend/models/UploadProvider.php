<?php
namespace app\modules\backend\models;

use app\models\Good\Good;
use app\models\Order;
use app\models\OrderProduct;
use app\models\Profile\Message;
use PHPExcel_IOFactory;
use yii\base\Model;
use Yii;
use yii\web\UploadedFile;

require_once dirname(__FILE__) . '/../../../Classes/PHPExcel.php';


class UploadProvider extends Model
{
    /**
     * @var $file UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function upload()
    {
        $products_counter = 0;
        $orders_table = [];
        $excelReader = PHPExcel_IOFactory::createReaderForFile($this->file->tempName);
        $excelObj = $excelReader->load($this->file->tempName);
        $rows = $excelObj->getActiveSheet()->toArray(null, true, true, true);

        $po_id = $rows[1]['B'];
        OrderProduct::updateAll([
            'confirmed_count' => 0,
        ], [
            'provider_order_id' => $po_id,
        ]);
        for ($i = 6; is_numeric($rows[$i]['A']); $i++) {
            $product_vendor = (string) $rows[$i]['C'];
            $products_count1 = $products_count = $rows[$i]['G'];
            foreach (OrderProduct::cachedFindAll([
                'provider_order_id' => $po_id,
                'product_vendor' => $product_vendor,
            ]) as $op) {
                /* @var $op OrderProduct */
                $orders_table[$op->order_id] = 1;
                $products_count -= $op->confirmed_count = min($products_count, $op->products_count);
                $op->save();
            }

            $products_counter += $products_count1 - $products_count;

            if ($products_count > 0) {
                Yii::$app->session->addFlash('error', $products_count == $products_count1 ?
                    "Товара с артикулом $product_vendor нет ни в одном заказе." :
                    "$products_count из $products_count1 товаров с артикулом $product_vendor лишние."
                );
            }
        }

        $full_count = 0;
        $partial_count = 0;
        $empty_count = 0;
        $total_count = count($orders_table);

        foreach (Order::cachedFindAll(['status' => Order::STATUS_PROVIDER_CHECKING]) as $order) {
            /* @var $order Order */
            $all_was = true;
            $all_full = true;
            $all_empty = true;
            foreach (OrderProduct::cachedFindAll(['order_id' => $order->id]) as $op) {
                if (is_null($op->confirmed_count)) {
                    $all_was = false;
                    break;
                }
                if ($op->confirmed_count != 0) {
                    $all_empty = false;
                }
                if ($op->confirmed_count != $op->products_count) {
                    $all_full = false;
                }
            }
            if ($all_was) {
                if ($all_full) {
                    $order->status = $all_full * Order::STATUS_CONFIRMED;
                    $full_count += 1;
                }
                elseif($all_empty) {
                    $order->status = $all_empty * Order::STATUS_UNCONFIRMED;
                    $empty_count += 1;
                }
                else {
                    $order->status = Order::STATUS_PARTIAL_CONFIRMED;
                    $partial_count += 1;
                }
                $order->save();
                $link = $order->getLink();
                $msg = new Message([
                    'user_id' => $order->user_id,
                    'text' => "$link был $order->status_str" . ($all_empty ? ' и должен быть оплачен до 23:30 ' .
                        date('d.m.Y', time()) : '')
                ]);
                $msg->sendEmail();
                $msg->save();
            }
        }
        Yii::$app->session->addFlash('success', "$products_counter товаров было распределено по $total_count заказам.");
        if ($full_count != 0)
            Yii::$app->session->addFlash('success', "$full_count заказов получило статус " .
                Order::$STATUS_TO_STRING[Order::STATUS_CONFIRMED]);
        if ($empty_count != 0)
            Yii::$app->session->addFlash('success', $empty_count . " заказов получило статус " .
                Order::$STATUS_TO_STRING[Order::STATUS_UNCONFIRMED]);
        if ($partial_count != 0)
            Yii::$app->session->addFlash('success', $partial_count . " заказов получило статус " .
                Order::$STATUS_TO_STRING[Order::STATUS_PARTIAL_CONFIRMED]);
    }
}
?>
