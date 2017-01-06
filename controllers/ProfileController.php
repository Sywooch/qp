<?php

namespace app\controllers;

use app\models\ResetPasswordForm;
use app\models\SetPasswordForm;
use app\models\SetPhoneForm;
use app\models\User;
use app\models\ValidatePhoneForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProfileController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        return $this->render('index', [
            'email' => $user->email,
            'phone' => $user->getPhone(),
        ]);
    }

    public function actionBookmark()
    {
        return $this->render('bookmark');
    }

    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        return $this->render('edit', [
            'email' => $user->email,
            'phone' => $user->getPhone(),
        ]);
    }

    public function actionPassword()
    {
        $key = Yii::$app->request->get('key');
        Yii::warning($key);
        $model = new SetPasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($key) {
                if ($user = User::findByPasswordResetToken($key)) {
                }
                else {
                    Yii::$app->session->setFlash('error', 'Неверный ключ сброса пароля');
                    return $this->goHome();
                }
            }
            else {
                if (!Yii::$app->user->isGuest) {
                    $user = Yii::$app->user->identity;
                }
                else {
                    return $this->goHome();
                }
            }
            $user->setPassword($model->password);
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
                return $this->goBack();
            }
            else {
                Yii::error('Возникла ошибка при смене пароля.');
            }
        }
        return $this->render('edit/password', [
            'model' => $model,
        ]);
    }

    public function actionPhone()
    {
        $model = new SetPhoneForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->setPhone($model->phone)) {
                Yii::$app->session->setFlash('success', 'На указанный телефон отправлено смс с кодом подтверждения.');
                return $this->redirect('validate-phone');
            }
            else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при установке номера телефона.');
                Yii::error('Возникла ошибка при установке номера телефона.');
            }
        }
        return $this->render('edit/phone', [
            'model' => $model,
        ]);
    }

    public function actionValidatePhone()
    {
        $user = Yii::$app->user->identity;
        if (!$user->phone_validation_key) {
            $this->goHome();
        }
        $model = new ValidatePhoneForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->session->setFlash('success', 'Телефонный номер успешно изменён.');
            return $this->goHome();
        }
        return $this->render('validate_phone', [
            'model' => $model,
        ]);

    }
}