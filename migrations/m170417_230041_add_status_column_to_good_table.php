<?php

use yii\db\Migration;

/**
 * Handles adding status to table `good`.
 */
class m170417_230041_add_status_column_to_good_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('good', 'status', $this->smallInteger());
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('good', 'status');
    }
}

