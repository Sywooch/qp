<?php

use yii\db\Migration;

/**
 * Handles the creation of table `property_value`.
 */
class m170214_073942_create_property_value_table extends Migration
{
    const TABLE_NAME = 'property_value';
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'c1id' => $this->string()->unique(),                  // only for dictionary type
            'property_id' => $this->integer(),
            'value' => $this->string()->notNull(),
        ], 'ENGINE InnoDB');

        $this->createIndex('idx-' . self::TABLE_NAME . '-property_id',
            self::TABLE_NAME,
            'property_id'
        );

        $this->addForeignKey('fk-' . self::TABLE_NAME . '-property_id',
             self::TABLE_NAME,
            'property_id',
            'good_property',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(  'fk-' . self::TABLE_NAME . '-property_id',  'property_dictionary');
        $this->dropIndex(       'idx-' . self::TABLE_NAME . '-property_id',  'property_dictionary');
        $this->dropTable(self::TABLE_NAME);
    }
}
