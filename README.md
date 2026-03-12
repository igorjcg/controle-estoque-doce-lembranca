# Controle de Estoque Doce Lembrança

Sistema web para controle de estoque e apoio ao processo de produção de doces, desenvolvido com Yii2. O projeto centraliza o cadastro de ingredientes, movimentações de entrada e saída, receitas e produção baseada em consumo de estoque, permitindo uma operação mais organizada e rastreável.

## Descrição do Sistema

O **Controle de Estoque Doce Lembrança** foi criado para apoiar o gerenciamento dos insumos utilizados na produção de doces. O sistema permite acompanhar o saldo de ingredientes com base nas movimentações registradas, cadastrar receitas com seus componentes e realizar produções que consomem automaticamente os itens do estoque.

O objetivo principal é reduzir erros operacionais, melhorar o controle dos custos e dar visibilidade sobre itens com estoque abaixo do mínimo.

## Funcionalidades

- Cadastro de ingredientes
- Controle de estoque por ingrediente
- Registro de movimentações de entrada
- Registro de movimentações de saída
- Cadastro de receitas
- Vínculo de ingredientes por receita
- Registro de produção com base em receita
- Baixa automática de estoque durante a produção
- Alerta de estoque baixo
- Listagens com filtros de apoio operacional

## Tecnologias Utilizadas

- PHP
- Yii2
- MySQL
- Composer
- Bootstrap

## Estrutura do Projeto

```text
assets/                 Recursos e definições de assets
commands/               Comandos de console
config/                 Configurações da aplicação
controllers/            Controllers da aplicação web
mail/                   Views de e-mail
models/                 Models e regras de negócio
runtime/                Arquivos gerados em execução
tests/                  Testes automatizados
views/                  Views da aplicação
web/                    Entry point e recursos públicos
vendor/                 Dependências do Composer
```

## Como Instalar Localmente

### 1. Clonar o projeto

```bash
git clone <url-do-repositorio>
cd controleDeEstoqueDoceLembranca
```

### 2. Instalar dependências

```bash
composer install
```

### 3. Verificar requisitos

Certifique-se de que o ambiente possui:

- PHP 7.4 ou superior
- Composer
- MySQL
- Extensões PHP necessárias para Yii2
- Servidor local, como XAMPP, WAMP ou o servidor embutido do PHP

## Configuração do Banco de Dados

Crie o banco de dados no MySQL, por exemplo:

```sql
CREATE DATABASE doce_lembranca CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Depois, ajuste o arquivo [config/db.php](/c:/dev/Codex/controleDeEstoqueDoceLembranca/config/db.php) com as credenciais locais:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=doce_lembranca',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```

Se estiver usando XAMPP com MySQL local, revise usuário, senha e porta conforme o seu ambiente.

## Executar Migrations

Com o banco configurado, execute as migrations para criar a estrutura das tabelas:

```bash
php yii migrate
```

Se estiver no Windows e usando o batch do Yii:

```bat
yii.bat migrate
```

## Executar o Projeto

Você pode executar o projeto de diferentes formas.

### Usando o servidor embutido do PHP

```bash
php yii serve
```

A aplicação ficará disponível em:

```text
http://localhost:8080
```

### Usando XAMPP

Configure o Apache para apontar para a pasta `web/` do projeto e acesse no navegador a URL correspondente ao seu ambiente local.

Exemplo:

```text
http://localhost/controleDeEstoqueDoceLembranca/web
```

## Licença

Este projeto utiliza como base o ecossistema Yii2 e segue o licenciamento definido pelos componentes e dependências utilizados. Caso este repositório faça parte de um projeto interno ou acadêmico, ajuste esta seção conforme a política de uso adotada.
