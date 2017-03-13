<?php

use yii\db\Migration;

/**
 * Handles the creation of table `provider_order`.
 */
class m170306_100938_create_provider_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('provider_order', [
            'id' => $this->primaryKey(),
            'pre_order_at' => $this->integer(),
            'order_at' => $this->integer(),
            'provider' => $this->string(),
        ], 'ENGINE InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('provider_order');
    }
}
