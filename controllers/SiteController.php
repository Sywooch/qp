<?php

namespace app\controllers;

use app\models\Bookmark;
use app\models\Good\Good;
use app\models\Good\Menu;
use app\models\Order;
use app\models\OrderProduct;
use Yii;
use yii\caching\TagDependency;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Profile\LoginForm;
use app\models\ContactForm;
use app\models\Profile\RegForm;
use app\models\Profile\ResetPasswordForm;
use app\models\User;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\helpers\Url;
use app\models\Profile\AccountActivation;
use DateTime;

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
                'only' => ['profile'],
                'denyCallback' => function($role, $action) {
                    Yii::$app->session->setFlash('warning',
                        ($action->id == 'logout' || $action->id == 'profile') ?
                        'Необходимо авторизоваться.' :
                        'Действие недоступно после авторизации.');

                    $this->goHome();
                },
                'rules' => [
                    [
                        'actions' => ['profile'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'eauth' => [
                // required to disable csrf validation on OpenID requests
                'class' => \nodge\eauth\openid\ControllerBehavior::className(),
                'only' => ['login'],
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

    const MAX_FAKE_CLIENTS = 1000;
    const DAYS_BEFORE_HALF_MAX = 100;
    public function actionIndex($is_discount = 0)
    {
        $start = new DateTime();
        $start->setDate(2017, 1, 1);
        $left_days = $start->diff(new DateTime())->days;
        $fake_clients = round(
                self::MAX_FAKE_CLIENTS *
                (1 - 1 / ($left_days / self::DAYS_BEFORE_HALF_MAX + 1))
            );

        $stats = [
            'clients' => $fake_clients + User::cachedGetCount(),
            'orders' => round($fake_clients * $fake_clients / 213) + Order::cachedGetCount(),
            'products' => round($fake_clients * $fake_clients / 27) + OrderProduct::cachedGetCount()
        ];


        $products = !$is_discount ?
            Yii::$app->db->cache(function ($db) {
                return Good::find()
                    ->select('good.*')
                    ->joinWith(Bookmark::tableName())
                    ->addSelect('COUNT(bookmark.id) AS bookmarkCount')
                    ->groupBy('good.id')
                    ->where(['status' => Good::STATUS_OK])
                    ->orderBy('bookmarkCount DESC')
                    ->limit(6)
                    ->all();
            }, null, new TagDependency(['tags' => 'cache_table_' . Bookmark::tableName()]))
            :
            Yii::$app->db->cache(function ($db) {
                return Good::find()->where(['status' => Good::STATUS_OK, 'is_discount' => true])->limit(6)->all();
            }, null, new TagDependency(['tags' => 'cache_table_' . Good::tableName()]));

        return $this->render('index', [
            'catalog' => Menu::getRoot(),
            'products' => $products,
            'stats' => $stats,
            'is_discount' => $is_discount,
        ]);
    }

    public function actionReg()
    {
        if (Yii::$app->user->can('user')) {
            return $this->goHome();
        }
        Yii::$app->user->logout();

        $model = new RegForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($user = $model->reg()) {
                if ($model->sendActivationEmail($user)) {
                    Yii::$app->session->setFlash('success', 'Письмо с дальнейшими инструкциями отправлено на email <strong>' .
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
        if (Yii::$app->user->can('user')) {
            return $this->goHome();
        }
        Yii::$app->user->logout();

        $serviceName = Yii::$app->getRequest()->getQueryParam('service');
        if (isset($serviceName)) {
            /** @var $eauth \nodge\eauth\ServiceBase */
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));

            try {
                if ($eauth->authenticate()) {
                    $identity = User::findByEAuth($eauth);
                    Yii::$app->user->login($identity, 0);

                    // special redirect with closing popup window
                    $eauth->redirect();
                }
                else {
                    // close popup window and redirect to cancelUrl
                    $eauth->cancel();
                }
            }
            catch (\nodge\eauth\ErrorException $e) {
                // save error to show it later
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());

                // close popup window and redirect to cancelUrl
//              $eauth->cancel();
                $eauth->redirect($eauth->getCancelUrl());
            }
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->user->login($model->getUser(), $model->rememberMe ? 3600*24*30 : 0);
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
        if(!$activation->getUser()->activateAccount()) {
            Yii::$app->session->setFlash('error', 'Ошибка активации.');
            Yii::error('Ошибка при активации.');
        }
        return $this->redirect(Url::to(['/site/login']));
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm(['scenario' => ContactForm::SCENARIO_USER_FEEDBACK]);
        if (Yii::$app->user->can('user')) {
            $model->email = Yii::$app->user->identity->email;
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['adminEmail']) && $model->save(false)) {
                Yii::$app->session->setFlash('contactFormSubmitted');
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка
                при сохранении и отправке сообения. ' . implode(' ', $model->getFirstErrors()));
            }
            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
            'feedbacks' => ContactForm::cachedFindAll([
                'status' => ContactForm::STATUS_VISIBLE
            ]),
        ]);
    }

    /**
     * Displays review page.
     *
     * @return string
     */
    public function actionReviews()
    {
        $model = new ContactForm(['scenario' => ContactForm::SCENARIO_USER_FEEDBACK]);
        if (Yii::$app->user->can('user')) {
            $model->email = Yii::$app->user->identity->email;
            $model->name = Yii::$app->user->identity->name;
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['adminEmail']) && $model->save(false)) {
                Yii::$app->session->setFlash('contactFormSubmitted');
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка
                при сохранении и отправке сообения. ' . implode(' ', $model->getFirstErrors()));
            }
            return $this->refresh();
        }

        return $this->render('review/index', [
            'model' => $model,
            'feedbacks' => ContactForm::cachedFindAll([
                'status' => ContactForm::STATUS_VISIBLE
            ]),
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

    public function actionResetPassword()
    {
        $model= new ResetPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = User::findByEmail($model->email)) {
                if ($model->sendEmail($user)) {
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте Email.');
                    return $this->goHome();
                }
                else{
                    Yii::$app->getSession()->setFlash('error', 'Ошибка при сбросе пароля.');
                }
            }
            else {
                Yii::$app->session->setFlash('error', 'Вы еще и почту не помните.');
            }
        }
        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    public function actionSearch($q = '')
    {
        return $this->render(
            'found',
            [
                'products' => Good::search($q),
                'categories' => Menu::search($q),
                'query' => $q
            ]
        );
    }
}
