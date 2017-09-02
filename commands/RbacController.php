<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return self::EXIT_CODE_NORMAL;
        }

        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $guest = $auth->createRole('guest');
        $auth->add($guest);

        $user = $auth->createRole('user');
        $auth->add($user);

        $manager = $auth->createRole('manager');
        $auth->add($manager);
        $auth->addChild($manager, $user);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manager);


        // usually implemented in your User model.
        $auth->assign($admin, 1);
    }
}