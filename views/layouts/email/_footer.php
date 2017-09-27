<?php
use yii\helpers\Html;
?>
<table style="font-family:arial;border-radius:5px 5px 5px 5px;max-width:870px;
background-color: #3e5763;margin-top:0; border-top: 40px solid #324853;
    padding-top: 30px;" border="0" cellpadding="5" cellspacing="0"">
    <tr>
        <td style="padding-bottom:5px;padding-left:15px;padding-right:40px">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:5px 0">
                        <span style="font-size:14px">
                            <a href="mailto:shop@qpvl.ru" rel="noopener" style="color:#ffffff" class="m_3280866808839791298ns-action" target="_blank">shop@qpvl.ru</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-size:18px">
                            <span style="color:#ffffff">
                                <?= isset(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : "Номер телефона"; ?>
                            </span>
                        </span>
                    </td>
                </tr>
            </table>
        </td>

        <td style="padding-right:15px;color:#ffffff;padding-left:0" align="right;">
            <table style="font-size:14px" align="right" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="right"><strong>Прием звонков:</strong>&nbsp;&nbsp;</td><td align="right"> ПН-ПТ: <?=Yii::$app->params['working_time']?></td>
                    <td align="right"><td align="right">СБ-ВС: <?=Yii::$app->params['weekend_working_time']?></td>
                    <td align="right"><b>Заказы онлайн:</b>&nbsp;&nbsp;</td><td align="right">круглосуточно</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="background-color:#324853">
            <table style="font-size:14px" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="font-family:'Open Sans', Arial, sans-serif; font-size:12px; line-height:18px; color:#848789;text-transform:uppercase;">
                        &copy; qpvl <?= date('Y') ?>
                    </td>
                    <td align="right"><?=Html::a('Правовая информация', ['/p/rules'])?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>