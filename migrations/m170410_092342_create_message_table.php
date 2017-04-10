<?php

use yii\db\Migration;

/**
 * Handles the creation of table `message`.
 */
class m170410_092342_create_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    const TABLE_NAME = 'message';

    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'text' => $this->text(),
        ], 'ENGINE InnoDB');

        $this->createIndex('idx-' . self::TABLE_NAME . '-user_id',
            self::TABLE_NAME, 'user_id');

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-user_id',
            self::TABLE_NAME, 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-user_id', self::TABLE_NAME);
        $this->dropIndex('idx-' . self::TABLE_NAME . '-user_id', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
