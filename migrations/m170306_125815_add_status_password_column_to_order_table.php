<?php

use yii\db\Migration;
use app\models\Order;
/**
 * Handles adding status to table `order`.
 */
class m170306_125815_add_status_password_column_to_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('order', 'status', $this->smallInteger()->defaultValue(Order::STATUS_NEW));
        $this->addColumn('order', 'password', $this->string());
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'password');
        $this->dropColumn('order', 'status');
    }
}
