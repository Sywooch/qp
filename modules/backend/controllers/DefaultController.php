<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use yii\filters\AccessControl;
use app\models\User;
/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'denyCallback' => function($role, $action) {
                    if (Yii::$app->user->isGuest) {
                        $this->redirect('/backend/default/login');
                        return;
                    }
                    Yii::$app->session->setFlash('error', 'Недостаточно прав.');
                    $this->goBack();
                },
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('index');
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/backend/default');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
