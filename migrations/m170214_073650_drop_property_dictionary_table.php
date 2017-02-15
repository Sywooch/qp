<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `property_dictionary`.
 */
class m170214_073650_drop_property_dictionary_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropForeignKey(  'fk-property_dictionary-property_id',  'property_dictionary');
        $this->dropIndex(       'idx-property_dictionary-property_id',  'property_dictionary');
        $this->dropIndex(       'idx-property_dictionary-c1id',         'property_dictionary');
        $this->dropTable('property_dictionary');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }
}
