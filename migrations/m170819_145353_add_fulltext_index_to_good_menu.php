<?php

use yii\db\Migration;

class m170819_145353_add_fulltext_index_to_good_menu extends Migration
{
    public function safeUp()
    {
        $this->addColumn('good', 'soundex_search', $this->string());
        $this->addColumn('menu', 'soundex_search', $this->string());
//        $this->execute("ALTER TABLE good ADD FULLTEXT INDEX search_index (soundex_search ASC)");
//        $this->execute("ALTER TABLE menu ADD FULLTEXT INDEX search_index (soundex_search ASC)");
    }

    public function safeDown()
    {
//        $this->execute("ALTER TABLE good DROP INDEX search_index;");
//        $this->execute("ALTER TABLE menu DROP INDEX search_index;");
        $this->dropColumn('good', 'soundex_search');
        $this->dropColumn('menu', 'soundex_search');
    }

}
