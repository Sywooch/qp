<?php

use yii\db\Migration;

/**
 * Handles adding product_vendor_and_provider to table `order_product`.
 */
class m170417_184901_add_product_vendor_and_provider_column_to_order_product_table extends Migration
{
    const TABLE_NAME = 'order_product';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'product_vendor', $this->string()->notNull());
        $this->addColumn(self::TABLE_NAME, 'provider', $this->string()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'provider');
        $this->dropColumn(self::TABLE_NAME, 'product_vendor');
    }
}
