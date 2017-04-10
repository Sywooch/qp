<?php

/* @var $this \yii\web\View */
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;

/* @var $content string */
?>
<section class="profile">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="visible-xs">
                    <?=Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ])?>
                </div>
                <div class="profile-nav">
                    <div class="title qp-collapse-handler" data-toggle="profile-box">
                        <h3>Личный кабинет <span class="arrow"></span></h3>
                        <div class="text-subline"></div>
                    </div>
                    <div class="qp-collapse" id="profile-box">
                        <?php
                        echo Nav::widget([
                            'options' => ['class' => 'nav nav-pills nav-stacked'],
                            'items' => [
                                ['label' => 'История покупок', 'url' => ['/profile/index']],
                                ['label' => 'Сообщения', 'url' => ['/profile/message']],
                                ['label' => 'Избранное', 'url' => ['/profile/bookmark']],
                                ['label' => 'Настройки профиля', 'url' => ['/profile/edit']],
                            ],
                        ]);
                        ?>
                    </div>

                </div>
            </div>
            <div class="col-sm-9">
                <div class="hidden-xs">
                    <?=Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ])?>
                </div>
                <?= $content ?>
            </div>
        </div>
    </div>
</section>