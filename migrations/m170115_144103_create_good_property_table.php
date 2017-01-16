<?php

use yii\db\Migration;

/**
 * Handles the creation of table `good_property`.
 */
class m170115_144103_create_good_property_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('good_property', [
            'id' => $this->primaryKey(),
            'c1id' => $this->string()->unique(),
            'name' => $this->string()->notNull(),
            'type' => $this->smallInteger()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('good_property');
    }
}
