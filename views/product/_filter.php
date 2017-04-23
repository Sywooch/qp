<?php
/** @var app\models\Good\GoodProperty $filter */
?>

<div class="filter__item">
    <span class="filter__item-title"><?=$filter['prop_name']?></span>
    <div class="text-subline"></div>
    <div class="filter__item-prop input-group-custom">
        <?php
        $i = 0;
        foreach ($filter['values'] as $item) :
        $r = rand(); $i++;
        ?>
            <div class="checkbox checkbox-success">
                <input type='checkbox' id="<?=$r. "-" . $i?>" class="styled" value="<?=$item['value_id']?>" data-name="<?=$filter['prop_id']?>">
                <label class="checkbox-inline checkbox-register" for="<?=$r. "-" . $i?>">
                    <?=$item['value_name']?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

</div>
