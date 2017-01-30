<?php
/** @var $product app\models\Good\Good */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Bookmark;

$img = Html::img([ $product->getImgPath() ],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['product/view', 'id' => $product->id];
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="product card">
        <?=Html::a($img, $url, ['class' => 'thumbnail'])?>
        <div class="caption">
            <div class="product-title">
                <?=Html::a($product->name, $url)?>
            </div>
        </div>
        <div class="product-panel">
            <div class="btn-group">
                <label class="product-price">
                    <?= (int)($product->price / 100) ?> руб.
                    <?= ($kop = $product->price % 100) ? $kop . 'коп.' : '' ?>
                </label>
                <input type="number" min="1" value="1"
                       name="product_count"
                       class="product_count"
                        data-product-id="<?= $product->id ?>">
                <input type="hidden" name="product_id" value=<?= $product->id ?>>
            </div>
            <button class="btn btn-icon btn-icon-left btn-success btn-compare"
                    data-product-id="<?= $product->id ?>"
                    data-product-count="1">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            </button>
            <?php
            $data = [
                'user_id' => Yii::$app->user->getId(),
                'product_id' => $product->getId(),
            ];
            $model = Bookmark::findOne($data);
            $form = ActiveForm::begin([
                'id' => 'bookmark',
                 'action' => [ $model ? 'catalog/delete-bookmark' : 'catalog/add-bookmark'],
            ]);
            $model = $model ? $model : new Bookmark($data);

            echo $form->field($model, 'user_id')->hiddenInput()->label(false);
            echo $form->field($model, 'product_id')->hiddenInput()->label(false);
            echo Html::submitButton($model->isNewRecord ?
                '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>' :
                '<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'
            );
            ActiveForm::end();
            ?>


        </div>
    </div>
</div>
