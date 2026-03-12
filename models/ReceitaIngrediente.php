<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Model da tabela "receita_ingrediente".
 */
class ReceitaIngrediente extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%receita_ingrediente}}';
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
            [['receita_id', 'ingrediente_id', 'unidade_medida_id', 'quantidade'], 'required'],
            [['receita_id', 'ingrediente_id', 'unidade_medida_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['quantidade'], 'number'],
            [['receita_id'], 'exist', 'skipOnError' => true, 'targetClass' => Receita::class, 'targetAttribute' => ['receita_id' => 'id']],
            [['ingrediente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ingrediente::class, 'targetAttribute' => ['ingrediente_id' => 'id']],
            [['unidade_medida_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadeMedida::class, 'targetAttribute' => ['unidade_medida_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'receita_id' => 'Receita',
            'ingrediente_id' => 'Ingrediente',
            'unidade_medida_id' => 'Unidade de Medida',
            'quantidade' => 'Quantidade',
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

    public function getIngrediente(): ActiveQuery
    {
        return $this->hasOne(Ingrediente::class, ['id' => 'ingrediente_id']);
    }

    public function getUnidadeMedida(): ActiveQuery
    {
        return $this->hasOne(UnidadeMedida::class, ['id' => 'unidade_medida_id']);
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
