<?php

use app\components\Html;
use yii\widgets\ActiveForm;

/* @var $id integer */
?>

<div class="manager-password">

    <form method="post" action=<?="order-password?id=$id"?> >
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
        Код подтверждения заказа:<br>
        <input type="text" name="password">
        <br>
        <input type="submit" value="Отправить">
    </form>

</div><!-- manager-password -->
