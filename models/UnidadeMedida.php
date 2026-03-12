<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Model da tabela "unidade_medida".
 */
class UnidadeMedida extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%unidade_medida}}';
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
            [['nome', 'sigla', 'categoria', 'fator_base'], 'required'],
            [['fator_base'], 'number'],
            [['flag_del', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['sigla'], 'string', 'max' => 10],
            [['categoria'], 'in', 'range' => ['peso', 'volume', 'unidade']],
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
            'sigla' => 'Sigla',
            'categoria' => 'Categoria',
            'fator_base' => 'Fator Base',
            'flag_del' => 'Excluído',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
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
