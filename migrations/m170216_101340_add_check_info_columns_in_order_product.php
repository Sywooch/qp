<?php

use yii\db\Migration;

class m170216_101340_add_check_info_columns_in_order_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order_product', 'old_price', $this->integer());
        $this->addColumn('order_product', 'product_name', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('order_product', 'product_name');
        $this->dropColumn('order_product', 'old_price');
    }
}
