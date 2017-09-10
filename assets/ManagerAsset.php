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
        'helpers/docxtemplater-latest.js',
        '//cdnjs.cloudflare.com/ajax/libs/jszip/2.6.1/jszip.js',
        '//fastcdn.org/FileSaver.js/1.1.20151003/FileSaver.js',
        '//cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.0.2/jszip-utils.js',

        'js/manager.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
