<?php

use yii\db\Migration;

/**
 * Handles the creation of table `feedback`.
 */
class m170214_213315_create_feedback_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('feedback', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull(),
            'body' => $this->string(\app\models\ContactForm::MAX_BODY_SIZE)->notNull(),
            'status' => $this->smallInteger()->notNull()
                ->defaultValue(\app\models\ContactForm::STATUS_UNMODERATED),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('feedback');
    }
}
