<?php

use yii\db\Migration;

class m170227_203502_add_public_id_column_in_order extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('order', 'public_id', $this->string());
        $this->addColumn('user', 'order_counter', $this->integer()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'order_counter');
        $this->dropColumn('order', 'public_id');
    }
}