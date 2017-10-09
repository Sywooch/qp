


<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Anton Kim</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => 'Загрузки', 'icon' => ' lnr lnr-download', 'url' => ['/backend/default/imports']],
                    ['label' => 'Настройки', 'icon' => ' lnr lnr-leaf', 'url' => ['/backend/default/config']],
                    ['label' => 'Статистика', 'icon' => ' lnr lnr-pie-chart', 'url' => ['/backend/default/report']],
                    ['label' => 'Мануал', 'icon' => ' lnr lnr-question-circle', 'url' => ['/backend/default/manual']],
                    ['label' => 'Пользователи', 'icon' => ' lnr lnr-users', 'url' => ['/backend/user']],
                    ['label' => 'Категории товаров', 'icon' => ' lnr lnr-list', 'url' => ['/backend/menu']],
                    ['label' => 'Товары', 'icon' => ' lnr lnr-layers', 'url' => ['/backend/good']],
                    ['label' => 'Заказы', 'icon' => ' icon lnr lnr-cart', 'url' => ['/backend/order']],
                    ['label' => 'Отзывы', 'icon' => ' lnr lnr-bubble', 'url' => ['/backend/feedback']],
                    ['label' => 'Архивы зак. пост.', 'icon' => ' lnr lnr-inbox',
                        'url' => ['/backend/default/provider-orders']
                    ],
                    ['label' => 'Login', 'url' => ['/default/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Для разработчиков',
                        'icon' => ' lnr lnr-code',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
