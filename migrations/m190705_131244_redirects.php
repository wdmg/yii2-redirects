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
            'request_url' => $this->string(255)->unique()->notNull(),
            'redirect_url' => $this->string(255)->notNull(),
            'code' => $this->integer(3)->notNull(),
            'description' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        if ($this->db->driverName === 'mysql') {
            $this->createIndex('{{%idx-redirects-requests}}', '{{%redirects}}', ['request_url(255)']);
            $this->createIndex('{{%idx-redirects-redirects}}', '{{%redirects}}', ['redirect_url(255)']);
        } else {
            $this->createIndex('{{%idx-redirects-requests}}', '{{%redirects}}', ['request_url']);
            $this->createIndex('{{%idx-redirects-redirects}}', '{{%redirects}}', ['redirect_url']);
        }

        $this->createIndex('{{%idx-redirects-status}}', '{{%redirects}}', ['code', 'is_active']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%redirects}}');
        $this->dropIndex('{{%idx-redirects-requests}}', '{{%redirects}}');
        $this->dropIndex('{{%idx-redirects-redirects}}', '{{%redirects}}');
        $this->dropIndex('{{%idx-redirects-status}}', '{{%redirects}}');
        $this->dropTable('{{%redirects}}');
    }

}
