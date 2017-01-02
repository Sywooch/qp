<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\User;
/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
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
        /*        if (!Yii::$app->user->isGuest) {
                    return $this->goHome();
                }
        */
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
