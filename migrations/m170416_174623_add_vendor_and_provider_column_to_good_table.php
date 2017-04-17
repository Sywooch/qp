<?php

use yii\db\Migration;

/**
 * Handles adding vendor_and_provider to table `good`.
 */
class m170416_174623_add_vendor_and_provider_column_to_good_table extends Migration
{
    const TABLE_NAME = 'good';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_NAME, 'vendor', $this->string()->unique()->notNull());
        $this->createIndex('idx-' . self::TABLE_NAME . '-vendor',
            self::TABLE_NAME, 'vendor', true);
        $this->addColumn(self::TABLE_NAME, 'provider', $this->string()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'provider');
        $this->dropIndex('idx-' . self::TABLE_NAME . '-vendor', self::TABLE_NAME);
        $this->dropColumn(self::TABLE_NAME, 'vendor');
    }
}
