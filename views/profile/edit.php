<?php
/** @var string $email */
/** @var string $phone */

use yii\helpers\Html;
$this->params['profileLayout'] = true;
$this->title = 'Настройки профиля';
$this->params['breadcrumbs'][] = [
    'label' => 'Личный кабинет',
    'url' => ['profile/index']
];
$this->params['breadcrumbs'][] = $this->title;

$items = [
        ['label' => 'Email', 'value' => $email, 'url' => ['']],
        ['label' => 'Телефон', 'value' => $phone, 'url' => ['profile/set-phone']],
        ['label' => 'Пароль', 'value' => '••••••••••••', 'url' => ['profile/set-password']],
];
?>

    <h1><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-8">
        <div class="profile-edit">
            <?php
            foreach ($items as $item) {
                echo $this->render('_itemEdit', ['item' => $item]);
            }
            ?>
        </div>

    </div>
</div>
