<?php

namespace app\controllers;

use app\models\OrderProduct;
use app\models\Profile\ResetPasswordForm;
use app\models\Profile\SetPasswordForm;
use app\models\Profile\SetPhoneForm;
use app\models\User;
use app\models\Profile\ValidatePhoneForm;
use app\models\Good\Good;
use app\models\Bookmark;
use app\models\Order;

use yii\data\ActiveDataProvider;
use Yii;
use yii\filters\AccessControl;
use yii\caching\TagDependency;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'pay' => ['POST'],
                    'cancel' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->user->identity->getOrders()]);
        Yii::$app->db->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, null, new TagDependency(['tags' => 'cache_table_' . Order::tableName()]));
        return $this->render('index', [
            'ordersDataProvider' => $dataProvider,
        ]);
    }

    public function actionViewOrder($id) {
        $order = Order::findOneOr404(['id' => $id, 'user_id' => Yii::$app->user->id ]);
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        return $this->render('order/view', [
            'products' => $products,
            'order' => $order,
        ]);
    }

    public function actionBookmark()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->user->identity->getBookmarks()->joinWith('product'),
        ]);
        Yii::$app->db->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, null, new TagDependency(['tags' => 'cache_table_' . Bookmark::tableName()]));
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
        $model = new SetPhoneForm( [ 'phone' => Yii::$app->user->identity->getPhone() ] );
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

    public function actionOrderRepeat($id)
    {
        $order = Order::cachedFindOne($id);
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        $cart = Yii::$app->cart;
        foreach($products as $p) {
            if ($prod_model = Good::findOneOr404(['c1id' => $p->product_c1id])) {
                // TODO check is product available
                $cart->put($prod_model->getCartPosition(), $p->products_count);
            }
            else {
                Yii::$app->session->addFlash('error', 'Товар ' . $p->product_name . ' недоступен');
            }

        }
        return $this->redirect('/cart');
    }
}
