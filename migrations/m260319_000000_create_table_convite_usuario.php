<?php

use yii\db\Migration;

class m260319_000000_create_table_convite_usuario extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%convite_usuario}}', [
            'id' => $this->primaryKey(),
            'token' => $this->string(64)->notNull(),
            'criado_por' => $this->integer()->notNull(),
            'criado_em' => $this->integer()->notNull(),
            'expira_em' => $this->integer()->notNull(),
            'usado' => $this->tinyInteger()->notNull()->defaultValue(0),
            'usado_por' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_convite_usuario_token', '{{%convite_usuario}}', 'token', true);
        $this->createIndex('idx_convite_usuario_criado_por', '{{%convite_usuario}}', 'criado_por');
        $this->createIndex('idx_convite_usuario_usado_por', '{{%convite_usuario}}', 'usado_por');

        $this->addForeignKey(
            'fk_convite_usuario_criado_por',
            '{{%convite_usuario}}',
            'criado_por',
            '{{%user}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk_convite_usuario_usado_por',
            '{{%convite_usuario}}',
            'usado_por',
            '{{%user}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_convite_usuario_usado_por', '{{%convite_usuario}}');
        $this->dropForeignKey('fk_convite_usuario_criado_por', '{{%convite_usuario}}');

        $this->dropIndex('idx_convite_usuario_usado_por', '{{%convite_usuario}}');
        $this->dropIndex('idx_convite_usuario_criado_por', '{{%convite_usuario}}');
        $this->dropIndex('idx_convite_usuario_token', '{{%convite_usuario}}');

        $this->dropTable('{{%convite_usuario}}');
    }
}
