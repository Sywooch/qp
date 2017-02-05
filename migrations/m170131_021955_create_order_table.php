<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m170131_021955_create_order_table extends Migration
{
    const TABLE_NAME = 'order';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
//             'status' => $this->smallInteger()->notNull(),
        ], 'ENGINE InnoDB');

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-user-id',
            self::TABLE_NAME, 'user_id', 'user', 'id');
    }



    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-user-id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
