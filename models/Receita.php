<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Model da tabela "receita".
 */
class Receita extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%receita}}';
    }

    /**
     * Por padrão, busca somente registros ativos (soft delete).
     */
    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['flag_del' => 0]);
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
            [['nome'], 'required'],
            [['descricao'], 'string'],
            [['flag_del', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nome'], 'string', 'max' => 150],
            [['flag_del'], 'default', 'value' => 0],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'flag_del' => 'Excluído',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
    }

    /**
     * Receita possui vários ingredientes cadastrados na tabela pivô.
     */
    public function getReceitaIngredientes(): ActiveQuery
    {
        return $this->hasMany(ReceitaIngrediente::class, ['receita_id' => 'id']);
    }

    /**
     * Receita pode ter várias produções registradas.
     */
    public function getProducoes(): ActiveQuery
    {
        return $this->hasMany(Producao::class, ['receita_id' => 'id']);
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
