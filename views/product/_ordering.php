<?php
$order = \app\models\Good\Good::$ORDERING_TO_STRING;
?>

<div class="sorter form-group">
    <form name='sort' method="get">
        <label>Сортировать</label>

        <select name="order" id="sort" class="form-control">
            <?php foreach ($order as $key => $value) : ?>
                <option value="<?=$key?>"><?=$value?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>


