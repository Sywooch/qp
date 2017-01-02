<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RegForm;
use app\models\SetPhoneForm;
use app\models\SetPasswordForm;
use app\models\ValidatePhoneForm;
use app\models\User;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\AccountActivation;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'login', 'reg', 'profile', 'validate_phone', 'set_phone'],
                'denyCallback' => function($role, $action) {
                    $this->goHome();
                },
                'rules' => [
                    [
                        'actions' => ['logout', 'profile', 'validate_phone', 'set_phone'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                    [
                        'actions' => ['login', 'reg'],
                        'allow' => false,
                        'roles' => ['user'],
                    ],
                    [
                        'actions' => ['login', 'reg'],
                        'allow' => true,
                        'roles' => ['guest'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new RegForm();

        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionReg()
    {
        $model = new RegForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($user = $model->reg()) {
                if ($model->sendActivationEmail($user)) {
                    Yii::$app->session->setFlash('success', 'Письмо с дальнейшими инструкциями отправлено на емайл <strong>' .
                        \yii\helpers\Html::encode($user->email) . '</strong> (проверьте папку спам).');

                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка. Письмо не отправлено.');
                    Yii::error('Ошибка отправки письма.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка при регистрации.');
                Yii::error('Ошибка при регистрации');
            }
        }
        return $this->render('reg', [
            'model' => $model
        ]);
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

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionActivateAccount($key)
    {
        try {
            $activation = new AccountActivation($key);
        }
        catch(InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if($activation->activateAccount()):
            Yii::$app->session->setFlash('success', 'Активация прошла успешно.');
        else:
            Yii::$app->session->setFlash('error', 'Ошибка активации.');
            Yii::error('Ошибка при активации.');
        endif;
        return $this->redirect(Url::to(['/site/login']));
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        return $this->render('profile', [
            'email' => $user->email,
            'phone' => $user->getPhone(),
        ]);
    }
    public function actionSetPassword()
    {
        $model = new SetPasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = Yii::$app->user->identity;
            $user->setPassword($model->password);
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
                return $this->goBack();
            }
            else {
                Yii::error('Возникла ошибка при смене пароля.');
            }
        }
        return $this->render('set_password', [
            'model' => $model,
        ]);
    }

    public function actionSetPhone()
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
        return $this->render('set_phone', [
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
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
