<?php

namespace app\modules\backend;
use yii\filters\AccessControl;
use Yii;

/**
 * admin module definition class
 */
class Admin extends \yii\base\Module
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['/backend/default/login'],
                'denyCallback' => function($role, $action) {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->getResponse()->redirect('/backend/default/login');
                    }
                    Yii::$app->session->setFlash('error', 'Недостаточно прав.');
                    Yii::$app->getResponse()->redirect('/');
                },

                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\backend\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->layout = 'main';

        // custom initialization code goes here
    }
}
