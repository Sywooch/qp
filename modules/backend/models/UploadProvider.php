<?php
namespace app\modules\backend\models;

use app\models\Good\Good;
use app\models\Order;
use app\models\OrderProduct;
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
        $excelReader = PHPExcel_IOFactory::createReaderForFile($this->file->tempName);
        $excelObj = $excelReader->load($this->file->tempName);
        $rows = $excelObj->getActiveSheet()->toArray(null, true, true, true);

        for ($i = 6; $rows[$i]['A'] == $i - 5; $i++) {
            $product_vendor = $rows[$i]['C'];
            $products_count1 = $products_count = $rows[$i]['G'];

            foreach (OrderProduct::cachedFindAll([
                'provider_order_id' => $rows[1]['D'],
                'product_vendor' => $product_vendor,
            ]) as $op) {
                /* @var $op OrderProduct */
                $products_count -= $op->confirmed_count = min($products_count, $op->products_count);
                $op->save();
            }
            if ($products_count > 0) {
                Yii::$app->session->setFlash('error', $products_count == $products_count1 ?
                    "Товара с артикулом $product_vendor нет ни в одном заказе." :
                    "$products_count из $products_count1 товаров с артикулом $product_vendor лишнии."
                );
            }
        }

        foreach (Order::cachedFindAll(['status' => Order::STATUS_PROVIDER_CHECKING]) as $order) {
            /* @var $order Order */
            $all_was = true;
            $all_full = true;
            foreach (OrderProduct::cachedFindAll(['order_id' => $order->id]) as $op) {
                if (is_null($op->confirmed_count)) {
                    $all_was = false;
                    break;
                }
                if ($op->confirmed_count != $op->products_count) {
                    $all_full = false;
                }
            }

            if ($all_was) {
                $order->status = $all_full ?
                    Order::STATUS_CONFIRMED:
                    Order::STATUS_PARTIAL_CONFIRMED;
                $order->save();
            }
        }
    }
}
?>