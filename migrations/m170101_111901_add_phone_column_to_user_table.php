<?php

use yii\db\Migration;

/**
 * Handles adding phone to table `user`.
 */
class m170101_111901_add_phone_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user', 'phone', $this->string()->unique());
        $this->addColumn('user', 'phone_validation_key', $this->string()->unique());
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'phone');
        $this->dropColumn('user', 'phone_validation_key');
    }
}
