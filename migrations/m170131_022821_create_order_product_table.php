<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_product`.
 */
class m170131_022821_create_order_product_table extends Migration
{
    const TABLE_NAME = 'order_product';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_c1id' => $this->string()->notNull(),
//            'product_name' => $this->string(),
//            'product_price' => $this->integer(),
            'products_count' => $this->integer(),
        ], 'ENGINE InnoDB');

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-order_id',
            self::TABLE_NAME, 'order_id', 'order', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-order_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
