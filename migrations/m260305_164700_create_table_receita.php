<?php

use yii\db\Migration;

class m260305_164700_create_table_receita extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%receita}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'descricao' => $this->text()->null(),
            'flag_del' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_receita_nome', '{{%receita}}', 'nome');
        $this->createIndex('idx_receita_created_by', '{{%receita}}', 'created_by');
        $this->createIndex('idx_receita_updated_by', '{{%receita}}', 'updated_by');

        $this->addForeignKey(
            'fk_receita_created_by',
            '{{%receita}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_receita_updated_by',
            '{{%receita}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_receita_updated_by', '{{%receita}}');
        $this->dropForeignKey('fk_receita_created_by', '{{%receita}}');

        $this->dropIndex('idx_receita_updated_by', '{{%receita}}');
        $this->dropIndex('idx_receita_created_by', '{{%receita}}');
        $this->dropIndex('idx_receita_nome', '{{%receita}}');

        $this->dropTable('{{%receita}}');
    }
}
