<?php

use yii\db\Migration;

class m170410_194237_add_rating_to_feedback_table extends Migration
{
    const TABLE = "feedback";
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'rating', $this->smallInteger());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, 'rating');
    }
}
