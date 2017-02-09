<?php
/** @var string $day */
/** @var string $date */
/** @var boolean $status */
?>
<div class="col-xs-6">
    <div class="item <?=$status ? 'free' : 'busy'?>">
        <div class="item-day"><i class="fa fa-calendar"></i>  <?=$day?></div>
        <div class="item-date"><?=$date?></div>
        <div class="row">
            <div class="col-xs-6 item-timebox">

                <div class="item-time"><i class="fa fa-clock-o"></i> 13:00 - 17:00</div>
                <div class="item-status">
                    <?php
                    echo $status ? '<span class="free">свободно</span>' : '<span class="busy">загружено</span>';
                    ?><br/>
                    <span class="item-status-limit">(Заказ до 00:00)</span>
                </div>

            </div>
            <div class="item-timebox  col-xs-6">

                <div class="item-time"><i class="fa fa-clock-o"></i> 17:00 - 21:00</div>
                <div class="item-status">
                    <?php
                    echo $status ? '<span class="free">свободно</span>' : '<span class="busy">загружено</span>';
                    ?><br/>
                    <span class="item-status-limit">(Заказ до 00:00)</span>
                </div>

            </div>
        </div>
    </div>
</div>