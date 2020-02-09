<?php

use yii\db\Migration;

/**
 * Class m190705_131244_redirects
 */
class m190705_131244_redirects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%redirects}}', [
            'id' => $this->primaryKey(),
            'section' => $this->string(128)->null(),
            'request_url' => $this->string(2048)->unique()->notNull(),
            'redirect_url' => $this->string(2048)->notNull(),
            'code' => $this->integer(3)->notNull(),
            'description' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
        $this->createIndex('{{%idx-redirects-urls}}', '{{%redirects}}', ['request_url', 'redirect_url', 'code', 'is_active']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%redirects}}');
        $this->dropIndex('{{%idx-redirects-urls}}', '{{%redirects}}');
        $this->dropTable('{{%redirects}}');
    }

}
