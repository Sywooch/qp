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
        $model = new SetPasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest) {
                return $this->goHome();
            }
            $user = Yii::$app->user->identity;
            $user->setPassword($model->password);
            if (!$user->save()) {
                Yii::error('Возникла ошибка при смене пароля.');
            }
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
            return $this->goBack();
        }
        else {
            if ($key = Yii::$app->request->get('key')) {
                if ($user = User::findByPasswordResetToken($key)) {
                    Yii::$app->user->login($user);
                }
                else {
                    Yii::$app->session->setFlash('error', 'Неверный ключ сброса пароля');
                    return $this->redirect('/site/login');
                }
            }

            return $this->render('edit/password', [
                'model' => $model,
            ]);
        }

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