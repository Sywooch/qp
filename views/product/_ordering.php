<?php
$order = \app\models\Good\Good::$ORDERING_TO_STRING;
?>

<form name='sort' method="get" class="form-inline">
    <div class="form-group">
        <label>Сортировать</label>
        <select name="order" id="sort" class="form-control">
            <?php foreach ($order as $key => $value) : ?>
                <option value="<?=$key?>"><?=$value?></option>
            <?php endforeach; ?>
        </select>
    </div>

        <div>
	        <label>Показывать по</label>
	        <select name="limit" id="limit" class="form-control">
	            <option value="<?=48?>">48</option>
	            <option value="<?=96?>">96</option>
	            <option value="<?=192?>">192</option>
	        </select>
        </div>
    </form>
    <br>
</div>


