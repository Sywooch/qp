<?php

use yii\db\Migration;

/**
 * Handles the creation of table `good`.
 */
class m170119_065126_create_good_table extends Migration
{
    const TABLE_NAME = 'good';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'measure' => $this->smallInteger(),
            'c1id' => $this->string()->unique(),
            'name' => $this->string(),
            'pic' => $this->string(),
            'price' => $this->integer(),
            'category_id' => $this->integer(),
            'properties' => $this->binary()
        ], 'ENGINE InnoDB');

        $this->createIndex('idx-' . self::TABLE_NAME . '-category_id',
            self::TABLE_NAME, 'category_id');

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-category_id',
            self::TABLE_NAME, 'category_id', 'menu', 'id');
                                                        //, 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-' . self::TABLE_NAME . '-category_id', self::TABLE_NAME);
        $this->dropIndex('idx-' . self::TABLE_NAME . '-category_id',
            self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
