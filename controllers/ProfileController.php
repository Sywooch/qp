<?php

namespace app\controllers;

use app\models\Profile\ResetPasswordForm;
use app\models\Profile\SetPasswordForm;
use app\models\Profile\SetPhoneForm;
use app\models\User;
use app\models\Profile\ValidatePhoneForm;
use app\models\Good\Good;

use yii\data\ActiveDataProvider;
use Yii;
use yii\filters\AccessControl;

class ProfileController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function($role, $action) {
                    $this->redirect('/site/login');
                },
                'except' => ['password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

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
        $dataProvider = new ActiveDataProvider([
            'query' => Good::find()->innerJoin('bookmark')->where([ 'bookmark.user_id' => Yii::$app->user->getId() ]),
        ]);
        return $this->render('bookmark', [
            'dataProvider' => $dataProvider,
        ]);
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
                return $this->redirect('/site/login');
            }
            $user = Yii::$app->user->identity;
            $user->setPassword($model->password);
            $user->removePasswordResetToken();
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
            if (Yii::$app->user->isGuest) {
                return $this->redirect('/site/login');
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
                return $this->redirect('/profile/confirm/phone');
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

    public function actionConfirmPhone()
    {
        $user = Yii::$app->user->identity;
        if (!$user->phone_validation_key) {
            $this->goHome();
        }
        $model = new ValidatePhoneForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->session->setFlash('success', 'Телефонный номер успешно изменён.');
            return $this->redirect('/profile/edit');
        }
        return $this->render('confirm/phone', [
            'model' => $model,
        ]);
    }
}
