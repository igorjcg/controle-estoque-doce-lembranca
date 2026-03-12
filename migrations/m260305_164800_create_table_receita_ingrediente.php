<?php

use yii\db\Migration;

class m260305_164800_create_table_receita_ingrediente extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%receita_ingrediente}}', [
            'id' => $this->primaryKey(),
            'receita_id' => $this->integer()->notNull(),
            'ingrediente_id' => $this->integer()->notNull(),
            'unidade_medida_id' => $this->integer()->notNull(),
            'quantidade' => $this->decimal(12, 3)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_receita_ingrediente_receita_id', '{{%receita_ingrediente}}', 'receita_id');
        $this->createIndex('idx_receita_ingrediente_ingrediente_id', '{{%receita_ingrediente}}', 'ingrediente_id');
        $this->createIndex('idx_receita_ingrediente_unidade_medida_id', '{{%receita_ingrediente}}', 'unidade_medida_id');
        $this->createIndex('idx_receita_ingrediente_created_by', '{{%receita_ingrediente}}', 'created_by');
        $this->createIndex('idx_receita_ingrediente_updated_by', '{{%receita_ingrediente}}', 'updated_by');

        $this->addForeignKey(
            'fk_receita_ingrediente_receita_id',
            '{{%receita_ingrediente}}',
            'receita_id',
            '{{%receita}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_receita_ingrediente_ingrediente_id',
            '{{%receita_ingrediente}}',
            'ingrediente_id',
            '{{%ingrediente}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_receita_ingrediente_unidade_medida_id',
            '{{%receita_ingrediente}}',
            'unidade_medida_id',
            '{{%unidade_medida}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_receita_ingrediente_created_by',
            '{{%receita_ingrediente}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_receita_ingrediente_updated_by',
            '{{%receita_ingrediente}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_receita_ingrediente_updated_by', '{{%receita_ingrediente}}');
        $this->dropForeignKey('fk_receita_ingrediente_created_by', '{{%receita_ingrediente}}');
        $this->dropForeignKey('fk_receita_ingrediente_unidade_medida_id', '{{%receita_ingrediente}}');
        $this->dropForeignKey('fk_receita_ingrediente_ingrediente_id', '{{%receita_ingrediente}}');
        $this->dropForeignKey('fk_receita_ingrediente_receita_id', '{{%receita_ingrediente}}');

        $this->dropIndex('idx_receita_ingrediente_updated_by', '{{%receita_ingrediente}}');
        $this->dropIndex('idx_receita_ingrediente_created_by', '{{%receita_ingrediente}}');
        $this->dropIndex('idx_receita_ingrediente_unidade_medida_id', '{{%receita_ingrediente}}');
        $this->dropIndex('idx_receita_ingrediente_ingrediente_id', '{{%receita_ingrediente}}');
        $this->dropIndex('idx_receita_ingrediente_receita_id', '{{%receita_ingrediente}}');

        $this->dropTable('{{%receita_ingrediente}}');
    }
}
