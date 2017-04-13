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
            if ($product = Good::cachedFindOne(['c1id' => $rows[$i]['C']])) {
                $products_count = $rows[$i]['G'];

                foreach (OrderProduct::cachedFindAll([
                    'provider_order_id' => $rows[1]['D'],
                    'product_c1id' => $product->c1id,
                ]) as $op) {
                    $op->confirmed_count = min($products_count, $op->products_count);
                    $op->save();
                    if ($op->products_count -= $products_count <= 0) {
                        break;
                    }
                }
            } else {
                Yii::$app->session->setFlash('error', 'Неизвестный артикул' . $rows[$i]['C']);
            }
        }
        foreach (Order::cachedFindAll(['status' => Order::STATUS_PROVIDER_CHECKING]) as $order) {
            $all_null = true;
            $all_succ = true;
            foreach (OrderProduct::cachedFindAll(['order_id' => $order->id]) as $op) {
                if ($op->confirmed_count) {
                    $all_null = false;
                }
                if ($op->confirmed_count != $op->products_count) {
                    $all_succ = false;
                }
            }
            $order->status = $all_null ?
                Order::STATUS_UNCONFIRMED : $all_succ ?
                    Order::STATUS_CONFIRMED:
                    Order::STATUS_PARTIAL_CONFIRMED;
            $order->save();
        }
    }
}
?>