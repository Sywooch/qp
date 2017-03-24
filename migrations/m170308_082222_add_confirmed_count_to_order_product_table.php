<?php

use yii\db\Migration;

class m170308_082222_add_confirmed_count_to_order_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('order_product', 'confirmed_count', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('order_product', 'confirmed_count');
    }
}
