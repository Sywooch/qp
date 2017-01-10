<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Menu*/

$this->title = $model->name;

//var_dump($model->parents()->all());exit;
foreach($model->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['menu/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div>
<?php

    foreach($model->children(1)->all() as $ch) {
        echo '<div>' . Html::a($ch->name, ['view', 'id' => $ch->id]) . '</div>';
    }
?>
<?= Html::a('<i>Добавить категорию</i>', ['create', 'par_id' => $model->id]) ?>
</div>
