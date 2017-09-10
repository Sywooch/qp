<?php

namespace app\modules\backend\controllers;

use app\components\Html;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

require_once dirname(__FILE__) . '/../../../Classes/PHPExcel.php';

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::findWithPaymentSum(),
        ]);
        $dataProvider->setSort([
            'attributes' =>
                array_keys((new User())->attributes) + [
                    'payment_sum' => [
                        'asc' => ['payment_sum' => SORT_ASC],
                        'desc' => ['payment_sum' => SORT_DESC],
                        'default' => SORT_DESC
                    ],
                ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => User::findOneOr404($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword(Yii::$app->request->post('password'));
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = User::findOneOr404($id);

        if ($model->load(Yii::$app->request->post()) ) {
            if (Yii::$app->user->id == $id && $model->role != 'admin') {
                    Yii::$app->session->setFlash('error',
                        'Невозможно снять статус администратора с текущего пользователя'
                    );
            }
            else {
                if ($model->save()){
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        User::findOneOr404($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionExcelExport()
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $rowCount = 1;

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'Payments');
        $rowCount++;

        $selector = User::findWithPaymentSum();
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

        foreach ($selector->all() as $user) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $user->id);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $user->email);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, Html::unstyled_price($user->payment_sum));
            $rowCount++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $file_name = '../temp/report.xlsx';
        $objWriter->save($file_name);
        set_time_limit(5*60);
        Yii::$app->response->sendFile($file_name);
    }
}
