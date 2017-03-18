<?php
namespace app\components;

use yii\bootstrap\Nav;
use yii\bootstrap\Widget;
use yii\bootstrap\ButtonDropdown;

class LoginWidget extends Widget
{

    public $mobile;

    public function init()
    {
        parent::init();

        if(\Yii::$app->user->isGuest) {
            echo  '<li>' . Html::a('Вход', ['/site/login'] ) . '</li>';
            echo  '<li>' . Html::a('Регистрация', ['/site/reg'] ) . '</li>';
        } else {
            $items = [
                ['label' => 'Личный кабинет', 'url' => '/profile/index'],
                ['label' => 'Избранное', 'url' => '/profile/bookmark'],
            ];

            if (\Yii::$app->user->can('manager')) {
                $items[] = ['label' => 'Панель менеджера', 'url' => '/manager'];
            }
            if (\Yii::$app->user->can('admin')) {
                $items[] = ['label' => 'Зазеркалье', 'url' => '/backend'];
            }

            if($this->mobile) {
                echo  '<li><span class="header">'. \Yii::$app->user->identity->email .'</span></li>';
                echo Nav::widget([
                    'items' => $items
                ]);
            } else {
                $items = array_merge($items, [
                    '<li role="separator" class="divider"></li>',
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Выйти',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                ]);

                echo '<li class="dropdown">'
                    . ButtonDropdown::widget([
                        'label' => \Yii::$app->user->identity->email,
                        'dropdown' => [
                            'items' => $items,
                        ]
                    ])
                    . '</li>';
            }

        }
    }
}