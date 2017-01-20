<?php

use yii\db\Migration;

/**
 * Handles the creation of table `property_dictionary`.
 */
class m170115_163619_create_property_dictionary_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('property_dictionary', [
            'id' => $this->primaryKey(),
            'c1id' => $this->string()->unique(),
            'property_id' => $this->integer(),
            'value' => $this->string()->notNull(),
        ]);
        $this->createIndex('idx-property_dictionary-c1id',
            'property_dictionary',
            'c1id'
        );
        $this->createIndex('idx-property_dictionary-property_id',
            'property_dictionary',
            'property_id'
        );
        $this->addForeignKey('fk-property_dictionary-property_id',
            'property_dictionary',
            'property_id',
            'good_property',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(  'fk-property_dictionary-property_id',  'property_dictionary');
        $this->dropIndex(       'idx-property_dictionary-property_id',  'property_dictionary');
        $this->dropIndex(       'idx-property_dictionary-c1id',         'property_dictionary');
        $this->dropTable('property_dictionary');
    }
}
