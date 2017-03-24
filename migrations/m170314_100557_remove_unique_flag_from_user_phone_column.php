<?php

use yii\db\Migration;

class m170314_100557_remove_unique_flag_from_user_phone_column extends Migration
{
    public function safeUp()
    {
        $this->dropIndex('phone', 'user');
        $this->dropIndex('phone_validation_key', 'user');
    }

    public function safeDown()
    {
    }

}
