<?php
use app\components\Html;
?>
<?php if(!\Yii::$app->user->isGuest) : ?>
<li role="separator" class="divider"></li>
<li>
    <?= Html::beginForm(['/site/logout'], 'post')
     . Html::submitButton( 'Выйти', ['class' => 'btn btn-link logout'])
     . Html::endForm()
    ?>
</li>
<?php endif; ?>

