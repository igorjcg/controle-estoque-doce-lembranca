<?php

use yii\db\Migration;

class m260305_165000_create_table_producao extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%producao}}', [
            'id' => $this->primaryKey(),
            'receita_id' => $this->integer()->notNull(),
            'quantidade' => $this->decimal(12, 3)->notNull(),
            'observacao' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_producao_receita_id', '{{%producao}}', 'receita_id');
        $this->createIndex('idx_producao_created_by', '{{%producao}}', 'created_by');
        $this->createIndex('idx_producao_updated_by', '{{%producao}}', 'updated_by');

        $this->addForeignKey(
            'fk_producao_receita_id',
            '{{%producao}}',
            'receita_id',
            '{{%receita}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_producao_created_by',
            '{{%producao}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_producao_updated_by',
            '{{%producao}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_producao_updated_by', '{{%producao}}');
        $this->dropForeignKey('fk_producao_created_by', '{{%producao}}');
        $this->dropForeignKey('fk_producao_receita_id', '{{%producao}}');

        $this->dropIndex('idx_producao_updated_by', '{{%producao}}');
        $this->dropIndex('idx_producao_created_by', '{{%producao}}');
        $this->dropIndex('idx_producao_receita_id', '{{%producao}}');

        $this->dropTable('{{%producao}}');
    }
}
