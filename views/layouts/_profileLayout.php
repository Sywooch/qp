<?php

/* @var $this \yii\web\View */
use yii\bootstrap\Nav;

/* @var $content string */
?>
<section class="profile">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="profile-nav">
                    <?php
                    echo Nav::widget([
                        'options' => ['class' => 'nav nav-pills nav-stacked'],
                        'items' => [
                            ['label' => 'История покупок', 'url' => ['/profile/index']],
                            ['label' => 'Избранное', 'url' => ['/profile/bookmark']],
                            ['label' => 'Настройки профиля', 'url' => ['/profile/edit']],
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-sm-9">
                <?= $content ?>
            </div>
        </div>
    </div>
</section>