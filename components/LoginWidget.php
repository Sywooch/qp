<?php
namespace app\components;

use yii\bootstrap\Widget;
use yii\bootstrap\ButtonDropdown;

class LoginWidget extends Widget
{
    public function init()
    {
        parent::init();

        if(\Yii::$app->user->isGuest) {
            echo  '<li>' . Html::a('Вход и регистрация', ['/site/login'] ) . '</li>';
        } else {
            $items = [
                ['label' => 'Личный кабинет', 'url' => '/profile/index'],
                ['label' => 'Избранное', 'url' => '/profile/bookmark'],
            ];

            if (\Yii::$app->user->can('admin')) {
                array_push($items, ['label' => 'Зазеркалье', 'url' => '/backend']);
            }

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