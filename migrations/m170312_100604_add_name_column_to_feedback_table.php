<?php

use yii\db\Migration;

/**
 * Handles adding name to table `feedback`.
 */
class m170312_100604_add_name_column_to_feedback_table extends Migration
{
    const TABLE = "feedback";
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'name', $this->string(32));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, 'name');
    }
}
