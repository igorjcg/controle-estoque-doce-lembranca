<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Model da tabela "producao".
 */
class Producao extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%producao}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['receita_id', 'quantidade'], 'required'],
            [['observacao'], 'string'],
            [['receita_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['quantidade'], 'number'],
            [['receita_id'], 'exist', 'skipOnError' => true, 'targetClass' => Receita::class, 'targetAttribute' => ['receita_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'receita_id' => 'Receita',
            'quantidade' => 'Quantidade',
            'observacao' => 'Observação',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
    }

    public function getReceita(): ActiveQuery
    {
        return $this->hasOne(Receita::class, ['id' => 'receita_id']);
    }

    public function getCriadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getAtualizadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
