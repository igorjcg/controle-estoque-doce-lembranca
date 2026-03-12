# Migrations – Controle de Estoque Doce Lembrança

Migrations alinhadas às **models** em `models/`. Use para recriar o banco do zero ou atualizar o schema.

## Tabelas (models → tabelas)

| Model              | Tabela               |
|--------------------|----------------------|
| User               | `user`               |
| UnidadeMedida      | `unidade_medida`     |
| Ingrediente        | `ingrediente`        |
| Receita            | `receita`            |
| ReceitaIngrediente | `receita_ingrediente`|
| MovimentacaoEstoque| `movimentacao_estoque` |
| Producao           | `producao`           |

## Ordem de execução

As migrations rodam na ordem do **nome do arquivo** (timestamp no início):

1. **m260305_164400_create_table_usuario** – Cria tabela `user` (id, username, email, password_hash, auth_key, status, created_at, updated_at, created_by, updated_by).
2. **m260305_164500_create_table_unidade_medida** – Cria `unidade_medida` (id, nome, sigla, categoria, fator_base, flag_del, timestamps, blameable). FK → user.
3. **m260305_164600_create_table_ingrediente** – Cria `ingrediente` (id, nome, unidade_medida_id, estoque_minimo_alerta, custo_medio, flag_del, timestamps, blameable). Sem coluna `estoque_atual` (estoque é calculado por movimentações). FK → unidade_medida, user.
4. **m260305_164700_create_table_receita** – Cria `receita` (id, nome, descricao, flag_del, timestamps, blameable). FK → user.
5. **m260305_164800_create_table_receita_ingrediente** – Cria `receita_ingrediente` (id, receita_id, ingrediente_id, unidade_medida_id, quantidade, timestamps, blameable). FK → receita, ingrediente, unidade_medida, user.
6. **m260305_164900_create_table_movimentacao_estoque** – Cria `movimentacao_estoque` (id, ingrediente_id, tipo_movimento, quantidade, valor_unitario, valor_total, observacao, timestamps, blameable). FK → ingrediente, user.
7. **m260305_165000_create_table_producao** – Cria `producao` (id, receita_id, quantidade, observacao, timestamps, blameable). FK → receita, user.

## Recriar o banco (fresh)

Para **apagar todas as tabelas** e rodar as migrations de novo:

```bash
php yii migrate/fresh
```

Com confirmação automática (útil em scripts/CI):

```bash
php yii migrate/fresh --interactive=0
```

## Aplicar migrations normalmente

```bash
php yii migrate
```

## Reverter migrations

Reverter a última:

```bash
php yii migrate/down
```

Reverter todas:

```bash
php yii migrate/down all
```

## Observações

- A model **User** usa a tabela **`user`** (não `usuario`). A migration `create_table_usuario` cria a tabela `user` com as colunas da model.
- **Ingrediente** não possui coluna `estoque_atual`; o estoque é obtido por `getEstoqueAtual()` a partir de `movimentacao_estoque`.
- Todas as tabelas têm `created_at`, `updated_at` (integer/timestamp) e, quando aplicável, `created_by`, `updated_by` (FK para `user.id`).
