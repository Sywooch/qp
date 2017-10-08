<?php

use yii\db\Migration;

class m171008_065818_add_archive_name_to_provider_order extends Migration
{
    public function safeUp()
    {
        $this->addColumn('provider_order', 'order_archive', $this->string(255));
        $this->addColumn('provider_order', 'pre_order_archive', $this->string(255));
    }

    public function safeDown()
    {
        $this->dropColumn('provider_order', 'order_archive');
        $this->dropColumn('provider_order', 'pre_order_archive');
    }
}
