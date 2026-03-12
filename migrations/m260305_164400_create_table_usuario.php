<?php

use yii\db\Migration;

/**
 * Cria tabela user (usuário).
 * Alinhada à model User: username, email, password_hash, auth_key, status, timestamps, blameable.
 */
class m260305_164400_create_table_usuario extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_user_username', '{{%user}}', 'username', true);
        $this->createIndex('idx_user_email', '{{%user}}', 'email', true);
        $this->createIndex('idx_user_created_by', '{{%user}}', 'created_by');
        $this->createIndex('idx_user_updated_by', '{{%user}}', 'updated_by');

        $this->addForeignKey(
            'fk_user_created_by',
            '{{%user}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_user_updated_by',
            '{{%user}}',
            'updated_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_updated_by', '{{%user}}');
        $this->dropForeignKey('fk_user_created_by', '{{%user}}');

        $this->dropIndex('idx_user_updated_by', '{{%user}}');
        $this->dropIndex('idx_user_created_by', '{{%user}}');
        $this->dropIndex('idx_user_email', '{{%user}}');
        $this->dropIndex('idx_user_username', '{{%user}}');

        $this->dropTable('{{%user}}');
    }
}
