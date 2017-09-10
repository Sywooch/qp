<?php

namespace app\modules\backend\controllers;

use app\models\Good\Good;
use app\models\Order;
use app\models\OrderProduct;
use app\models\User;
use app\modules\backend\models\UploadProvider;
use app\modules\backend\models\UploadZipModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Profile\LoginForm;

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
                    'download-provider' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $userTotal = User::find()->count();
        $orderTotal = Order::find()->count();
        $goodTotal = Good::find()->count();
        $salesTotal = User::find()->count();

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
        return $this->render('imports', [
            'model' => $model,
            'provider' => $provider
        ]);
    }

    public function actionDownloadProvider()
    {
        $date = Yii::$app->request->post('date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $arch = "../temp/provider-order/$date.zip";
        if (file_exists($arch)) {
            set_time_limit(5*60);
            Yii::$app->response->sendFile($arch);
        }
        else {
            Yii::$app->session->setFlash('error', "Архив за $date не найден");
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

    public function actionReport()
    {
        $start = Yii::$app->request->post('start');
        $end = Yii::$app->request->post('end');
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
}
