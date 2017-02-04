<?php
use yii\helpers\Html;
use app\components\catalog\ProductWidget;
$query = yii\helpers\Html::encode($query);

$this->params['breadcrumbs'][] = ['label' => 'Блог', 'url' => ['/blog']];
$this->title = "Результаты поиска по запросу \"$query\"";
$this->params['breadcrumbs'][] = $this->title;

app\modules\search\SearchAssets::register($this);
$this->registerJs("jQuery('.search').highlight('{$query}');");
?>

<div class="row">
    <div class="col-md-6 col-md-offset-2">

        <?php
        Yii::$app->search->index();
        if (!empty($hits)):
            foreach ($hits as $hit):
                $arr = [];
                $doc = $hit->getDocument();
                foreach($doc->getFieldNames() as $key) {
                    $arr[$key] = $doc->getField($key)->value;
                }
                // TODO: make other widget for search result rendering
                echo ProductWidget::widget([
                    'product' => new \app\models\Good\Good($arr)
                ]);
            endforeach;
        else:
            ?>
            <div class="alert alert-danger"><h3>По запросу "<?= $query ?>" ничего не найдено!</h3></div>
            <?php
        endif;

        echo yii\widgets\LinkPager::widget([
            'pagination' => $pagination,
        ]);
        ?>
    </div>
</div>
?>