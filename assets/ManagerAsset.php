<?php

namespace app\assets;

use yii\web\AssetBundle;
class ManagerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css',
        'css/manager.css',
    ];
    public $js = [
        '//cdn.jsdelivr.net/momentjs/latest/moment.min.js',
        '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js',
        'js/manager.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
