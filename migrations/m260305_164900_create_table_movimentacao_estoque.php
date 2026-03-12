<?php

use yii\db\Migration;

class m260305_164900_create_table_movimentacao_estoque extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%movimentacao_estoque}}', [
            'id' => $this->primaryKey(),
            'ingrediente_id' => $this->integer()->notNull(),
            'tipo_movimento' => "ENUM('entrada','saida') NOT NULL",
            'quantidade' => $this->decimal(12, 3)->notNull(),
            'valor_unitario' => $this->decimal(12, 4)->null(),
            'valor_total' => $this->decimal(12, 4)->null(),
            'observacao' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_movimentacao_estoque_ingrediente_id', '{{%movimentacao_estoque}}', 'ingrediente_id');
        $this->createIndex('idx_movimentacao_estoque_created_at', '{{%movimentacao_estoque}}', 'created_at');
        $this->createIndex('idx_movimentacao_estoque_created_by', '{{%movimentacao_estoque}}', 'created_by');
        $this->createIndex('idx_movimentacao_estoque_updated_by', '{{%movimentacao_estoque}}', 'updated_by');

        $this->addForeignKey(
            'fk_movimentacao_estoque_ingrediente_id',
            '{{%movimentacao_estoque}}',
            'ingrediente_id',
            '{{%ingrediente}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_movimentacao_estoque_created_by',
            '{{%movimentacao_estoque}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_movimentacao_estoque_updated_by',
            '{{%movimentacao_estoque}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_movimentacao_estoque_updated_by', '{{%movimentacao_estoque}}');
        $this->dropForeignKey('fk_movimentacao_estoque_created_by', '{{%movimentacao_estoque}}');
        $this->dropForeignKey('fk_movimentacao_estoque_ingrediente_id', '{{%movimentacao_estoque}}');

        $this->dropIndex('idx_movimentacao_estoque_updated_by', '{{%movimentacao_estoque}}');
        $this->dropIndex('idx_movimentacao_estoque_created_by', '{{%movimentacao_estoque}}');
        $this->dropIndex('idx_movimentacao_estoque_created_at', '{{%movimentacao_estoque}}');
        $this->dropIndex('idx_movimentacao_estoque_ingrediente_id', '{{%movimentacao_estoque}}');

        $this->dropTable('{{%movimentacao_estoque}}');
    }
}
