<?php
$order = \app\models\Good\Good::$ORDERING_TO_STRING;
?>

<div class= form-group">
    <form name='sort' method="get">
    	<div>
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
	            <option value="<?=48?>">24</option>
	            <option value="<?=96?>">48</option>
	            <option value="<?=192?>">96</option>
	        </select>
        </div>
    </form>
    <br>
</div>


