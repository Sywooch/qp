<?php
/** @var string $icon */
/** @var integer $number */
/** @var string $text */
?>

<div class="col-md-4 col-sm-4 col-xs-12">
    <div class="stat__list-item item">
        <div class="item__icon">
            <img src="<?= \yii\helpers\Url::to('@web/img/' . $icon) ?>"/>
        </div>
        <div class="item__content">
            <h3 class="item__content-number">
                <?=$number?>
            </h3>
            <div class="item__content-text">
                <?=$text?>
            </div>
        </div>
    </div>
</div>