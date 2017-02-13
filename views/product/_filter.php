<?php
/** @var string $title */
/** @var array $options */
?>

<div class="filter__item">
    <span class="filter__item-title"><?=$title?></span>
    <div class="text-subline"></div>
    <div class="input-group-custom">
        <?php  if($options['type'] == 10 || $options['type'] == 0) :
            $i = 0; $name = rand(100, 999);
            ?>
            <?php foreach ($options['value'] as $option) : $r = rand(); $i++;?>
                <div class="checkbox checkbox-success">
                    <input type='checkbox' name="<?=$r?>" id="<?=$r. " - " . $i?>" class="styled" value="<?=$r?>">
                    <label class="checkbox-inline checkbox-register" for="<?=$r. " - " . $i?>">
                        <?=$option?>
                    </label>
                </div>
            <?php endforeach; ?>
        <?php endif;?>
    </div>

</div>
