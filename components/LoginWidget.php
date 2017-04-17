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

        $cart = "<li class=\"shopping\">".\app\components\CartWidget::widget()."</li>";

        if(\Yii::$app->user->isGuest) {
            echo  '<li>' . Html::a('Вход', ['/site/login'] ) . '</li>';
            echo  '<li>' . Html::a('Регистрация', ['/site/reg'] ) . '</li>';
            if($this->mobile) {
                echo  '<li>' . Html::a('Главная', ['/'] ) . '</li>';
                echo $cart;
            }
        } else {
            $profile = 'Личный кабинет';
            if($this->mobile) {
                $profile .= '<span class="side-nav__mail">'. \Yii::$app->user->identity->email .'</span>';
            }
            $items = [
                $this->mobile ? '<li><div class=\'divider\'></div></li>' : '',
                ['label' => $profile, 'url' => '/profile/index'],
                ['label' => 'Избранное', 'url' => '/profile/bookmark'],
            ];

            if (\Yii::$app->user->can('manager')) {
                $items[] = ['label' => 'Панель менеджера', 'url' => '/manager'];
            }
            if (\Yii::$app->user->can('admin')) {
                $items[] = ['label' => 'Зазеркалье', 'url' => '/backend'];
            }

            if($this->mobile) {
                echo  '<li></li>';
                echo Nav::widget([
                    'encodeLabels' => false,
                    'items' => array_merge([
                        ['label' => 'Главная', 'url' => ['/']],
                    ], [ $cart], $items)
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
