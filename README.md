# Controle de Estoque Doce Lembranca

Sistema web para controle de estoque e apoio ao processo de producao de doces, desenvolvido com Yii2. O projeto centraliza o cadastro de ingredientes, movimentacoes de entrada e saida, receitas e producao baseada em consumo de estoque, permitindo uma operacao mais organizada e rastreavel.

## Descricao do Sistema

O **Controle de Estoque Doce Lembranca** foi criado para apoiar o gerenciamento dos insumos utilizados na producao de doces. O sistema permite acompanhar o saldo de ingredientes com base nas movimentacoes registradas, cadastrar receitas com seus componentes e realizar producoes que consomem automaticamente os itens do estoque.

O objetivo principal e reduzir erros operacionais, melhorar o controle dos custos e dar visibilidade sobre itens com estoque abaixo do minimo.

## Funcionalidades

- Cadastro de ingredientes
- Controle de estoque por ingrediente
- Registro de movimentacoes de entrada
- Registro de movimentacoes de saida
- Cadastro de receitas
- Vinculo de ingredientes por receita
- Registro de producao com base em receita
- Baixa automatica de estoque durante a producao
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
assets/                 Recursos e definicoes de assets
commands/               Comandos de console
config/                 Configuracoes da aplicacao
controllers/            Controllers da aplicacao web
mail/                   Views de e-mail
models/                 Models e regras de negocio
runtime/                Arquivos gerados em execucao
tests/                  Testes automatizados
views/                  Views da aplicacao
web/                    Entry point e recursos publicos
vendor/                 Dependencias do Composer
```

## Como Instalar Localmente

### 1. Clonar o projeto

```bash
git clone <url-do-repositorio>
cd controleDeEstoqueDoceLembranca
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Verificar requisitos

Certifique-se de que o ambiente possui:

- PHP 7.4 ou superior
- Composer
- MySQL
- Extensoes PHP necessarias para Yii2
- Servidor local, como XAMPP, WAMP ou o servidor embutido do PHP

## Configuracao do Banco de Dados

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

Se estiver usando XAMPP com MySQL local, revise usuario, senha e porta conforme o seu ambiente.

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

Voce pode executar o projeto de diferentes formas.

### Usando o servidor embutido do PHP

```bash
php yii serve
```

A aplicacao ficara disponivel em:

```text
http://localhost:8080
```

### Usando XAMPP

Configure o Apache para apontar para a pasta `web/` do projeto e acesse no navegador a URL correspondente ao seu ambiente local.

Exemplo:

```text
http://localhost/controleDeEstoqueDoceLembranca/web
```

## Licenca

Este projeto utiliza como base o ecossistema Yii2 e segue o licenciamento definido pelos componentes e dependencias utilizados. Caso este repositorio faca parte de um projeto interno ou academico, ajuste esta secao conforme a politica de uso adotada.
