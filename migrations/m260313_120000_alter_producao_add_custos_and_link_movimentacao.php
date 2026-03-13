<?php

use yii\db\Migration;

class m260313_120000_alter_producao_add_custos_and_link_movimentacao extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%producao}}', 'custo_unitario', $this->decimal(12, 4)->notNull()->defaultValue(0));
        $this->addColumn('{{%producao}}', 'custo_total', $this->decimal(12, 4)->notNull()->defaultValue(0));

        $this->addColumn('{{%movimentacao_estoque}}', 'producao_id', $this->integer()->null()->after('ingrediente_id'));
        $this->createIndex('idx_movimentacao_estoque_producao_id', '{{%movimentacao_estoque}}', 'producao_id');
        $this->addForeignKey(
            'fk_movimentacao_estoque_producao_id',
            '{{%movimentacao_estoque}}',
            'producao_id',
            '{{%producao}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_movimentacao_estoque_producao_id', '{{%movimentacao_estoque}}');
        $this->dropIndex('idx_movimentacao_estoque_producao_id', '{{%movimentacao_estoque}}');
        $this->dropColumn('{{%movimentacao_estoque}}', 'producao_id');

        $this->dropColumn('{{%producao}}', 'custo_total');
        $this->dropColumn('{{%producao}}', 'custo_unitario');
    }
}
