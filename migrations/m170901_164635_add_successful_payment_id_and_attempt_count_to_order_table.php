<?php

use yii\db\Migration;

class m170901_164635_add_successful_payment_id_and_attempt_count_to_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('order', 'payment_id', $this->string()->unique());
        $this->createIndex('idx-order-payment_id', 'order', [ 'payment_id' ]);
        $this->addColumn('order', 'attempt_count', $this->integer()->defaultValue(0));
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'attempt_count');
        $this->dropIndex('idx-order-payment_id', 'order');
        $this->dropColumn('order', 'payment_id');
    }
}
