<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Model da tabela "movimentacao_estoque".
 * Campos: ingrediente_id, tipo_movimento (entrada/saida), quantidade (sempre unidade base),
 * valor_unitario, valor_total, created_at.
 */
class MovimentacaoEstoque extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%movimentacao_estoque}}';
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
            [['ingrediente_id', 'tipo_movimento', 'quantidade'], 'required'],
            [['observacao'], 'string'],
            [['ingrediente_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['quantidade', 'valor_unitario', 'valor_total'], 'number'],
            [['tipo_movimento'], 'in', 'range' => ['entrada', 'saida']],
            [['ingrediente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ingrediente::class, 'targetAttribute' => ['ingrediente_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'ingrediente_id' => 'Ingrediente',
            'tipo_movimento' => 'Tipo de Movimento',
            'quantidade' => 'Quantidade',
            'valor_unitario' => 'Valor Unitário',
            'valor_total' => 'Valor Total',
            'observacao' => 'Observação',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // valor_total = quantidade * valor_unitario (precisão decimal, não float).
        if ($this->valor_unitario !== null && $this->valor_unitario !== '' && ($this->valor_total === null || $this->valor_total === '')) {
            $this->valor_total = bcmul((string) $this->quantidade, (string) $this->valor_unitario, 6);
        }
        return true;
    }

    public function getIngrediente(): ActiveQuery
    {
        return $this->hasOne(Ingrediente::class, ['id' => 'ingrediente_id']);
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
