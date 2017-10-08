<?php

namespace app\modules\backend\controllers;

use app\models\Good\Good;
use app\models\Order;
use app\models\OrderProduct;
use app\models\ProviderOrder;
use app\models\User;
use app\modules\backend\models\UploadProvider;
use app\modules\backend\models\UploadZipModel;
use app\modules\backend\models\ConfigForm;
use PHPExcel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Writer_Excel2007;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Profile\LoginForm;
use app\components\Html;

require_once dirname(__FILE__) . '/../../../Classes/PHPExcel.php';

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'upload-provider' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $userTotal = User::find()->count();
        $orderTotal = Order::find()->count();
        $goodTotal = Good::find()->count();
        $users = User::findWithPaymentSum()->all();

        $salesTotal = array_reduce($users, function($sum, $user) {
            return $sum + $user->payment_sum;
        }, 0);

        return $this->render('index', [
            'userTotal' => $userTotal,
            'orderTotal' => $orderTotal,
            'salesTotal' => $salesTotal,
            'goodTotal' => $goodTotal,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */

    public function actionImports()
    {
        $model = new UploadZipModel();
        $provider = new UploadProvider();

        if (Yii::$app->request->isPost) {
            $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
            if ($model->upload()) {
                yii::$app->session->addFlash('success', 'Архив принят на обработку');
            }
        }
        ProviderOrder::flushCache();
        $arches = ProviderOrder::cachedFindAll();

        $arches =
            array_map(function($x) {return $x->order_archive;}, $arches) +
            array_map(function($x) {return $x->pre_order_archive;}, $arches);
        $arches = array_filter(array_unique($arches, SORT_LOCALE_STRING), function($x) {return !is_null($x);});
        rsort($arches);
        return $this->render('imports', [
            'model' => $model,
            'provider' => $provider,
            'arches' => $arches,
        ]);
    }

    public function actionDownloadProvider()
    {
        $arch = Yii::$app->request->get('arch');
        if (!$arch) {
            $date = Yii::$app->request->post('date');
            if (!$date) {
                $date = date('Y-m-d');
            }
            $arch = "$date.zip";
        }
        $full_name = "../temp/provider-order/" . $arch;

        if (file_exists($full_name)) {
            set_time_limit(5*60);
            Yii::$app->response->sendFile($full_name);
        }
        else {
            Yii::$app->session->setFlash('error', "Архив $arch не найден");
            $this->redirect('/backend/default/imports');
        }
    }

    public function actionUploadProvider()
    {
        $provider = new UploadProvider();
        $provider->file = UploadedFile::getInstance($provider, 'file');
        $provider->upload();
        return $this->redirect('/backend/default/imports');
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/backend/default/imports');
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->user->login($model->getUser(), $model->rememberMe ? 3600*24*30 : 0);
            return $this->redirect('/backend/default');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionManual()
    {
        return $this->render('manual');
    }

    public function reportSelector($start, $end)
    {
        $order_products = OrderProduct::find()->joinWith('order')
            ->select(["provider, product_name, product_vendor, product_c1id, old_price,
                SUM(products_count) AS count_by_c1id,
                SUM(products_count) - SUM(confirmed_count) AS unconfirmed_count_by_c1id"])
            ->where(['not', ['confirmed_count' => null]])
            ->groupBy('provider, product_name, product_vendor, product_c1id, old_price');
        if ($start) {
            $start_timestamp = strtotime($start);
            $order_products->andWhere(['>=', 'order.created_at', $start_timestamp]);
        }
        if ($end) {
            $end_timestamp = strtotime($end) + 24 * 60 * 60;
            $order_products->andWhere(['<=', 'order.created_at', $end_timestamp]);
        }
        return $order_products;
    }

    public function actionReport()
    {
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $order_products = $this->reportSelector($start, $end);
        $dataProvider = new ActiveDataProvider([
            'query' => $order_products,
        ]);

        $dataProvider->setSort([
            'attributes' =>
                array_keys((new OrderProduct())->attributes) + [
                    'count_by_c1id' => [
                        'asc' => ['count_by_c1id' => SORT_ASC],
                        'desc' => ['count_by_c1id' => SORT_DESC],
                        'default' => SORT_DESC
                    ],
                    'unconfirmed_count_by_c1id' => [
                        'asc' => ['count_by_c1id' => SORT_ASC],
                        'desc' => ['count_by_c1id' => SORT_DESC],
                        'default' => SORT_DESC
                    ],
                ]
        ]);

        return $this->render('report', [
            'dataProvider' => $dataProvider,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function actionReportExcelExport()
    {
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $rowCount = 1;

        $objPHPExcel->getActiveSheet()->getStyle('C:C')
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'Поставщик');
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'Цена');
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'Название товара');
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, 'Артикул');
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, '1С ИД');
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, 'Заказано товара');
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, 'Нехватка товара');
        $rowCount++;

        $selector = $this->reportSelector($start, $end);
        if ($sort = Yii::$app->request->get('sort')) {
            if ($sort[0] == '-') {
                $ordering = SORT_DESC;
                $sort = substr($sort, 1);
            }
            else {
                $ordering = SORT_ASC;
            }

            $selector->orderBy([$sort => $ordering]);
        }

        foreach ($selector->all() as $product) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $product->provider);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, Html::unstyled_price($product->old_price));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $product->product_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $product->product_vendor);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $product->product_c1id);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $product->count_by_c1id);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $product->unconfirmed_count_by_c1id);
            $rowCount++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $file_name = '../temp/report.xlsx';
        $objWriter->save($file_name);
        set_time_limit(5*60);
        Yii::$app->response->sendFile($file_name);
    }

    public function actionConfig()
    {
        $file = dirname(__FILE__) . '/../../../config/params.inc';
        $model = new ConfigForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $config = $model->getAttributes();
            $str = serialize($config);
            file_put_contents($file, $str);
            Yii::$app->session->addFlash('success', 'Настройки сохранены.');
        }
        else {
            $content = file_get_contents($file);
            $arr = unserialize($content);
            $model->setAttributes($arr);
        }
        return $this->render('config',['model'=>$model]);
    }
}
?>
