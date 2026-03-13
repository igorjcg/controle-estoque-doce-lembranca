<?php

namespace app\models;

use app\common\util\UnidadeUtil;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
            [['quantidade', 'custo_unitario', 'custo_total'], 'number'],
            [['quantidade'], 'compare', 'compareValue' => 0, 'operator' => '>'],
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
            'custo_unitario' => 'Custo Unitário',
            'custo_total' => 'Custo Total',
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

    public function getMovimentacoesEstoque(): ActiveQuery
    {
        return $this->hasMany(MovimentacaoEstoque::class, ['producao_id' => 'id']);
    }

    public function salvarComMovimentacoes(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = static::getDb()->beginTransaction();

        try {
            $dados = $this->calcularConsumoEValidarEstoque();

            $this->custo_unitario = $dados['custo_unitario'];
            $this->custo_total = $dados['custo_total'];

            if (!$this->save(false)) {
                throw new Exception('Falha ao salvar produção.');
            }

            MovimentacaoEstoque::deleteAll(['producao_id' => $this->id]);

            foreach ($dados['itens'] as $item) {
                $movimentacao = new MovimentacaoEstoque();
                $movimentacao->producao_id = $this->id;
                $movimentacao->ingrediente_id = $item['ingrediente']->id;
                $movimentacao->tipo_movimento = 'saida';
                $movimentacao->quantidade = $item['quantidade_total'];
                $movimentacao->valor_unitario = (string) $item['ingrediente']->custo_medio;
                $movimentacao->valor_total = $item['custo_total_ingrediente'];
                $movimentacao->observacao = $this->observacao ?: "Baixa automática da produção da receita: {$dados['receita']->nome}";

                if (!$movimentacao->save()) {
                    throw new Exception("Falha ao registrar movimentação do ingrediente {$item['ingrediente']->nome}.");
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('quantidade', $e->getMessage());
            return false;
        }
    }

    public function excluirComMovimentacoes(): bool
    {
        $transaction = static::getDb()->beginTransaction();

        try {
            MovimentacaoEstoque::deleteAll(['producao_id' => $this->id]);
            $this->delete();
            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public static function registrar(int $receitaId, float $quantidade, ?string $observacao = null): self
    {
        $model = new static();
        $model->receita_id = $receitaId;
        $model->quantidade = $quantidade;
        $model->observacao = $observacao;

        if (!$model->salvarComMovimentacoes()) {
            $mensagem = $model->getFirstError('quantidade') ?: 'Não foi possível registrar a produção.';
            throw new Exception($mensagem);
        }

        return $model;
    }

    protected function calcularConsumoEValidarEstoque(): array
    {
        $receita = Receita::find()
            ->where(['id' => (int) $this->receita_id])
            ->with(['receitaIngredientes.ingrediente.unidadeMedida', 'receitaIngredientes.unidadeMedida'])
            ->one();

        if ($receita === null) {
            throw new Exception('Receita não encontrada.');
        }

        $itensReceita = $receita->receitaIngredientes;
        if ($itensReceita === []) {
            throw new Exception('A receita não possui ingredientes.');
        }

        $movimentacoesAtuais = [];
        if (!$this->isNewRecord) {
            foreach ($this->movimentacoesEstoque as $movimentacao) {
                $movimentacoesAtuais[$movimentacao->ingrediente_id] = ($movimentacoesAtuais[$movimentacao->ingrediente_id] ?? 0) + (float) $movimentacao->quantidade;
            }
        }

        $itens = [];
        $custoUnitario = 0.0;

        foreach ($itensReceita as $item) {
            $ingrediente = $item->ingrediente;
            $unidadeOrigem = $item->unidadeMedida;
            $unidadeBaseIngrediente = $ingrediente?->unidadeMedida;

            if ($ingrediente === null || $unidadeOrigem === null || $unidadeBaseIngrediente === null) {
                throw new Exception('Ingrediente ou unidade de medida inválidos na receita.');
            }

            $quantidadeUnitariaEmBase = UnidadeUtil::converterParaBase(
                (float) $item->quantidade,
                $unidadeOrigem,
                $unidadeBaseIngrediente
            );
            $quantidadeTotal = $quantidadeUnitariaEmBase * (float) $this->quantidade;

            $estoqueDisponivel = $ingrediente->getEstoqueAtual() + ($movimentacoesAtuais[$ingrediente->id] ?? 0);
            if ($estoqueDisponivel < $quantidadeTotal) {
                throw new Exception("Estoque insuficiente para o ingrediente {$ingrediente->nome}.");
            }

            $custoUnitarioIngrediente = $quantidadeUnitariaEmBase * (float) $ingrediente->custo_medio;
            $custoTotalIngrediente = $quantidadeTotal * (float) $ingrediente->custo_medio;
            $custoUnitario += $custoUnitarioIngrediente;

            $itens[] = [
                'ingrediente' => $ingrediente,
                'quantidade_total' => round($quantidadeTotal, 3),
                'custo_total_ingrediente' => round($custoTotalIngrediente, 4),
            ];
        }

        return [
            'receita' => $receita,
            'itens' => $itens,
            'custo_unitario' => round($custoUnitario, 4),
            'custo_total' => round($custoUnitario * (float) $this->quantidade, 4),
        ];
    }
}
