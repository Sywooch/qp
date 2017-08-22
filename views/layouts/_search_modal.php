<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="modal fade search-overlay" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="search-modal">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="lnr lnr-cross btn-close"></span></button>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body search">
                <?= Html::beginForm(Url::to(['/site/search']), 'get', ['class' => 'form form-search']) ?>
                <div class="input-group">
                    <?= Html::textInput('q', '', ['class' => 'form-control input-lg js-search', 'placeholder' => 'Поиск...', 'id' => 'search-input']) ?>
                    <span class="input-group-addon">
                        <?= Html::submitButton('<span class="lnr lnr-magnifier"></span>', ['class' => 'search__btn']) ?>
                    </span>
                </div><!-- /input-group -->
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
