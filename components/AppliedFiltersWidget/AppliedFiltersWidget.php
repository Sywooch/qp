<?php

namespace app\components\AppliedFiltersWidget;

use yii\base\Widget;

class AppliedFiltersWidget extends Widget
{

    public $filters;

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        return $this->render('applied-filters', [

        ]);
    }

}