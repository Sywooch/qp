<?php

use yii\bootstrap\Html;

$menu = [
    [
        "label" => "Каталог",
        "url" => "/"
    ], [
        "label" => "Акции",
        "url" => "/"
    ], [
        "label" => "Контакты",
        "url" => "/"
    ],
];
?>

<tr>
    <td height="40" class="em_height">&nbsp;</td>
</tr>
<tr>
    <td align="center"><a href="https://qpvl.ru" target="_blank" style="text-decoration:none;">
            <img src="https://www.qpvl.ru/img/logo.gif" width="230" height="80" style="display:block;font-family: Arial, sans-serif; font-size:15px; line-height:18px; color:#30373b;  font-weight:bold;" border="0" alt="qpvl" /></a>
    </td>
</tr>
<tr>
    <td height="14" style="font-size:12px; line-height:1px;">Интернет-супермаркет</td>
</tr>
<!-- === //LOGO SECTION === -->
<!-- === NEVIGATION SECTION === -->
<tr>
    <td align="center" style="font-family:'Open Sans', Arial, sans-serif; font-size:15px; line-height:18px; color:#30373b; text-transform:uppercase; font-weight:bold;" class="em_font">
        <?php foreach ($menu as $item) {
            echo Html::a($item['label'], $item['url'], ['target' => "_blank", "style" => "text-decoration:none; color:#30373b;"]). "&nbsp;&nbsp;&nbsp;&nbsp;";
        }?>
    </td>
</tr>