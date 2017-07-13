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

        '//cdnjs.cloudflare.com/ajax/libs/docxtemplater/3.1.3/docxtemplater.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
        '//fastcdn.org/FileSaver.js/1.1.20151003/FileSaver.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.0.2/jszip-utils.min.js',

        'js/manager.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
