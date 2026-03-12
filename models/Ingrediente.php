<?php

namespace app\models;

use app\common\util\Util;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Model da tabela "ingrediente".
 * Estoque atual é calculado a partir das movimentações (não armazenado).
 */
class Ingrediente extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%ingrediente}}';
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
            [['nome', 'unidade_medida_id', 'estoque_minimo_alerta'], 'required'],
            [['unidade_medida_id', 'flag_del', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['estoque_minimo_alerta', 'custo_medio'], 'number'],
            [['custo_medio'], 'default', 'value' => 0],
            [['nome'], 'string', 'max' => 150],
            [['flag_del'], 'default', 'value' => 0],
            [['unidade_medida_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadeMedida::class, 'targetAttribute' => ['unidade_medida_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'unidade_medida_id' => 'Unidade de Medida Base',
            'estoque_minimo_alerta' => 'Estoque Mínimo para Alerta',
            'custo_medio' => 'Custo Médio',
            'flag_del' => 'Excluído',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
    }

    /**
     * Estoque atual calculado a partir das movimentações (soma entradas - soma saídas).
     * Quantidade sempre na unidade base. Retorna string para uso com bcmath (precisão decimal).
     */
    public function getEstoqueAtualDecimal(): string
    {
        $valor = static::getDb()
            ->createCommand(
                'SELECT COALESCE(SUM(CASE WHEN tipo_movimento = :entrada THEN quantidade ELSE -quantidade END), 0) 
                 FROM {{%movimentacao_estoque}} WHERE ingrediente_id = :id',
                [':entrada' => 'entrada', ':id' => $this->id]
            )
            ->queryScalar();

        return (string) $valor;
    }

    /**
     * Estoque atual (float para exibição/compatibilidade).
     */
    public function getEstoqueAtual(): float
    {
        return (float) $this->getEstoqueAtualDecimal();
    }

    public function getEstoqueAtualFormatado(): string
    {
        return $this->formatarQuantidade($this->getEstoqueAtual());
    }

    public function getEstoqueAtualComUnidadeFormatado(): string
    {
        return Util::formatQuantidadeComUnidade($this->getEstoqueAtual(), $this->unidadeMedida->sigla ?? null);
    }

    public function getEstoqueMinimoAlertaFormatado(): string
    {
        return $this->formatarQuantidade((float) $this->estoque_minimo_alerta);
    }

    public function getEstoqueMinimoAlertaComUnidadeFormatado(): string
    {
        return Util::formatQuantidadeComUnidade((float) $this->estoque_minimo_alerta, $this->unidadeMedida->sigla ?? null);
    }

    /**
     * Ingrediente possui uma unidade base.
     */
    public function getUnidadeMedida(): ActiveQuery
    {
        return $this->hasOne(UnidadeMedida::class, ['id' => 'unidade_medida_id']);
    }

    public function getReceitaIngredientes(): ActiveQuery
    {
        return $this->hasMany(ReceitaIngrediente::class, ['ingrediente_id' => 'id']);
    }

    public function getMovimentacoesEstoque(): ActiveQuery
    {
        return $this->hasMany(MovimentacaoEstoque::class, ['ingrediente_id' => 'id']);
    }

    public function getCriadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getAtualizadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    protected function formatarQuantidade(float $quantidade): string
    {
        return Util::formatDecimalTrimmed($quantidade, 3, ',', '');
    }

}
