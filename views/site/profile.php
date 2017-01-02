<?php

use yii\helpers\Html;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <p> <?php echo 'Email:' . $email ?> </p>
    <p> <?php echo 'Номер телефона:' . $phone . Html::a('Изменить', ['/site/set-phone'], ['class'=>'btn btn-primary grid-button'])?> </p>
    <p> <?php echo 'Пароль:' . Html::a('Изменить', ['/site/set-password'], ['class'=>'btn btn-primary grid-button'])?> </p>

</div>
