<?php

namespace app\components\AppliedFiltersWidget;

use yii\web\AssetBundle;

class AppliedFiltersWidgetAssets extends AssetBundle
{

    public $css = [
    ];
    public $js = [
        'js/AppliedFilters.js'

    ];
    public $depends = [
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }

}