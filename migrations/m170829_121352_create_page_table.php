<?php

use yii\db\Migration;

/**
 * Handles the creation of table `page`.
 */
class m170829_121352_create_page_table extends Migration
{
    const TABLE_NAME = 'pages';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string()->notNull(),
                'alias' => $this->string()->notNull(),
                'published' => $this->boolean()->defaultValue(1),
                'content' => $this->text(),
                'title_browser' => $this->string(),
                'meta_keywords' => $this->string(200),
                'meta_description' => $this->string(160),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('alias', self::TABLE_NAME, ['alias'], true);
        $this->createIndex('alias_and_published', self::TABLE_NAME, ['alias', 'published']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
