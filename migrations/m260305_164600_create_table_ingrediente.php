<?php

use yii\db\Migration;

class m260305_164600_create_table_ingrediente extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%ingrediente}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'unidade_medida_id' => $this->integer()->notNull(),
            'estoque_minimo_alerta' => $this->decimal(12, 3)->notNull()->defaultValue(0),
            'custo_medio' => $this->decimal(12, 4)->notNull()->defaultValue(0),
            'flag_del' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_ingrediente_nome', '{{%ingrediente}}', 'nome');
        $this->createIndex('idx_ingrediente_unidade_medida_id', '{{%ingrediente}}', 'unidade_medida_id');
        $this->createIndex('idx_ingrediente_created_by', '{{%ingrediente}}', 'created_by');
        $this->createIndex('idx_ingrediente_updated_by', '{{%ingrediente}}', 'updated_by');

        $this->addForeignKey(
            'fk_ingrediente_unidade_medida_id',
            '{{%ingrediente}}',
            'unidade_medida_id',
            '{{%unidade_medida}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_ingrediente_created_by',
            '{{%ingrediente}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_ingrediente_updated_by',
            '{{%ingrediente}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ingrediente_updated_by', '{{%ingrediente}}');
        $this->dropForeignKey('fk_ingrediente_created_by', '{{%ingrediente}}');
        $this->dropForeignKey('fk_ingrediente_unidade_medida_id', '{{%ingrediente}}');

        $this->dropIndex('idx_ingrediente_updated_by', '{{%ingrediente}}');
        $this->dropIndex('idx_ingrediente_created_by', '{{%ingrediente}}');
        $this->dropIndex('idx_ingrediente_unidade_medida_id', '{{%ingrediente}}');
        $this->dropIndex('idx_ingrediente_nome', '{{%ingrediente}}');

        $this->dropTable('{{%ingrediente}}');
    }
}
