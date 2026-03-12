<?php

use yii\db\Migration;

class m260305_164500_create_table_unidade_medida extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%unidade_medida}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(100)->notNull(),
            'sigla' => $this->string(10)->notNull(),
            'categoria' => "ENUM('peso','volume','unidade') NOT NULL",
            'fator_base' => $this->decimal(12, 4)->notNull(),
            'flag_del' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_unidade_medida_created_by', '{{%unidade_medida}}', 'created_by');
        $this->createIndex('idx_unidade_medida_updated_by', '{{%unidade_medida}}', 'updated_by');

        $this->addForeignKey(
            'fk_unidade_medida_created_by',
            '{{%unidade_medida}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk_unidade_medida_updated_by',
            '{{%unidade_medida}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_unidade_medida_updated_by', '{{%unidade_medida}}');
        $this->dropForeignKey('fk_unidade_medida_created_by', '{{%unidade_medida}}');

        $this->dropIndex('idx_unidade_medida_updated_by', '{{%unidade_medida}}');
        $this->dropIndex('idx_unidade_medida_created_by', '{{%unidade_medida}}');

        $this->dropTable('{{%unidade_medida}}');
    }
}
