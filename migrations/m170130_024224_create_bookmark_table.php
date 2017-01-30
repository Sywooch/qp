<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bookmark`.
 */
class m170130_024224_create_bookmark_table extends Migration
{
    const TABLE_NAME = 'bookmark';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
        ], 'ENGINE InnoDB');

        $this->createIndex('idx-' . self::TABLE_NAME . '-user_id',
            self::TABLE_NAME, 'user_id');

        $this->createIndex('idx-' . self::TABLE_NAME . '-user_id-product_id',
            self::TABLE_NAME, [ 'user_id', 'product_id' ], [ 'unique' => true ]);

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-user_id',
            self::TABLE_NAME, 'user_id', 'user', 'id', 'CASCADE');

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-product_id',
            self::TABLE_NAME, 'product_id', 'good', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-product_id', self::TABLE_NAME);
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-user_id', self::TABLE_NAME);
        $this->dropIndex('idx-' . self::TABLE_NAME . '-user_id-product_id', self::TABLE_NAME);
        $this->dropIndex('idx-' . self::TABLE_NAME . '-user_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
