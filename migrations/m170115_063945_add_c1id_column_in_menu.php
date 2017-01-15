<?php

use yii\db\Migration;

class m170115_063945_add_c1id_column_in_menu extends Migration
{
    public function safeUp()
    {
        $this->addColumn('menu', 'c1id', $this->string()->unique());
        $this->createIndex('idx-menu-c1id', 'menu', [ 'c1id' ]);
    }

    public function safeDown()
    {
        $this->dropIndex('idx-menu-c1id', 'menu');
        $this->dropColumn('menu', 'c1id');
    }
}
