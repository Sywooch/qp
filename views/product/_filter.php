<?php
/** @var app\models\Good\GoodProperty $filter */
?>

<div class="filter__item">
    <span class="filter__item-title"><?=$filter['prop_name']?></span>
    <div class="text-subline"></div>
    <div class="filter__item-prop input-group-custom">
        <?php
        foreach ($filter['values'] as $item) :
            $pref = "filter_".$item['value_id']."_".$filter['prop_id'];
        ?>
            <div class="checkbox checkbox-success">
                <input type='checkbox'
                       id = "<?=$pref?>"
                       class = "styled"
                       value = "<?=$item['value_id']?>"
                       data-name = "<?=$filter['prop_id']?>"
                       data-title = "<?=$filter['prop_name']?>"
                >
                <label class="checkbox-inline checkbox-register" for="<?=$pref?>">
                    <?=$item['value_name']?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

</div>
