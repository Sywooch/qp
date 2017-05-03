<?php

use yii\db\Migration;

/**
 * Handles adding is_discount to table `good`.
 */
class m170503_025150_add_is_discount_column_to_good_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('good', 'is_discount', $this->boolean()->defaultValue(false));
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('good', 'is_discount');
    }
}
