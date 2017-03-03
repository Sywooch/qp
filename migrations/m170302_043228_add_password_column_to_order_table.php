<?php

use yii\db\Migration;

/**
 * Handles adding password to table `order`.
 */
class m170302_043228_add_password_column_to_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'phone', $this->string()->unique());
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'phone');
    }
}
