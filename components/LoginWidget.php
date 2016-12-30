<?php
namespace app\components;

use yii\bootstrap\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;

class LoginWidget extends Widget
{
    public function init()
    {
        parent::init();

        if(!\Yii::$app->user->isGuest) {
            echo '<li class="dropdown">'
                . ButtonDropdown::widget([
                    'label' => \Yii::$app->user->identity->email,
                    'dropdown' => [
                        'items' => [
                            ['label' => 'Личный кабинет', 'url' => '/'],
                            '<li>'
                            . Html::beginForm(['/site/logout'], 'post')
                            . Html::submitButton(
                                'Выйти',
                                ['class' => 'btn btn-link logout']
                            )
                            . Html::endForm()
                            . '</li>'
                        ],
                    ]
                ])
                . '</li>';
        } else {
            echo ['label' => 'Вход и регистрация', 'url' => ['/site/login']];
        }

    }
}