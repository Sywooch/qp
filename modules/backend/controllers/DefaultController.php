<?php

namespace app\modules\backend\controllers;

use app\modules\backend\models\UploadZipModel;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Profile\LoginForm;
use app\models\Good\Menu;
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
        $model = new UploadZipModel();

        if (Yii::$app->request->isPost) {
            $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
            if ($model->upload()) {
                yii::$app->session->setFlash('success', 'Архив принят на обработку');
            }
        }
        return $this->render('index', ['model' => $model, 'par' => Menu::getRoot()]);
    }

    public function actionProviderOrder()
    {
        $date = date('Y-m-d');
        $arch = "provider-order/$date.zip";
        if (file_exists($arch)) {
            set_time_limit(5*60);
            Yii::$app->response->sendFile($arch);
        }
        else {
            Yii::$app->session->setFlash('error', "Архив за $date не найден");
            return $this->refresh();
        }
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->user->login($model->getUser(), $model->rememberMe ? 3600*24*30 : 0);
            return $this->redirect('/backend/default');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
