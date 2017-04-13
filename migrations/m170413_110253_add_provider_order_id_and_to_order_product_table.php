<?php

use yii\db\Migration;

class m170413_110253_add_provider_order_id_and_to_order_product_table extends Migration
{
    const TABLE_NAME = "order_product";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'provider_order_id', $this->integer());
        $this->addForeignKey('fk-' . self::TABLE_NAME . '-provider_order_id',
            self::TABLE_NAME, 'provider_order_id', 'provider_order', 'id', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-provider_order_id', self::TABLE_NAME);
        $this->dropColumn(self::TABLE_NAME, 'provider_order_id');
    }
}