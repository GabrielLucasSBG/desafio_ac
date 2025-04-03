Este documento fornece instruções detalhadas sobre como configurar, executar e testar o projeto de sistema de transações
desenvolvido com Laravel.

## Sumário

1. [Requisitos do Sistema](#requisitos-do-sistema)
2. [Configuração do Projeto](#configura%C3%A7%C3%A3o-do-projeto)
3. [Executando com Docker](#executando-com-docker)
4. [Executando Localmente](#executando-localmente)
5. [Testes Automatizados](#testes-automatizados)
6. [Documentação com PHPDoc](#documenta%C3%A7%C3%A3o-com-phpdoc)
7. [Estrutura do Projeto](#estrutura-do-projeto)

## Requisitos do Sistema

- PHP 8.4
- Composer
- Docker e Docker Compose (para execução em contêineres)
- Git
- MySQL
- Node.js e NPM

## Configuração do Projeto

### Clonagem do Repositório

``` bash
git clone [URL_DO_REPOSITÓRIO]
cd desafio_ac
```

### Instalação de Dependências

``` bash
# Instalação das dependências PHP
composer install

# Instalação das dependências JavaScript
npm install
```

### Configuração do Ambiente

``` bash
# Copiar o arquivo de ambiente de exemplo
cp .env.example .env

# Gerar a chave da aplicação
php artisan key:generate
```

Edite o arquivo `.env` para configurar:

- Conexão com o banco de dados (MySQL)
- Configurações de filas (Queue)
- Outras configurações específicas da aplicação

### Preparação do Banco de Dados

``` bash
# Executar as migrações para criar a estrutura do banco de dados
php artisan migrate

# (Opcional) Popular o banco de dados com dados de exemplo
php artisan db:seed
```

## Executando com Docker

O projeto está configurado para ser executado em contêineres Docker, simplificando a configuração do ambiente.

### Iniciando os Contêineres

``` bash
# Construir e iniciar os contêineres
docker-compose up -d
```

O aplicativo estará disponível em `http://localhost:{SuaPorta}`.

### Executando Comandos no Contêiner

``` bash
docker-compose exec app php artisan key:generate

# Executar migrações dentro do contêiner
docker-compose exec app php artisan migrate

# Executar seeds dentro do contêiner
docker-compose exec app php artisan db:seed

# Acessar o shell do contêiner
docker-compose exec app bash
```

### Parando os Contêineres

``` bash
docker-compose down
```

## Executando Localmente

Se preferir executar o projeto sem Docker:

### Servidor de Desenvolvimento

``` bash
php artisan serve
```

O servidor estará disponível em `http://localhost:8000`.

### Processamento de Filas

``` bash
php artisan queue:work
```

### Compilação de Assets

``` bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

## Testes Automatizados

O projeto utiliza PHPUnit para testes automatizados.

### Executando Todos os Testes

``` bash
php artisan test
```

### Executando Testes Específicos

``` bash
# Executar testes de uma classe específica
php artisan test --filter=TransactionControllerTest

# Executar um método de teste específico
php artisan test --filter=test_transfer_successful
```

## Documentação com PHPDoc

O projeto utiliza PHPDocumentor para a geração automática de documentação.

### Instalação do PHPDocumentor

O PHPDocumentor já está incluído como dependência no projeto através do pacote `phpdocumentor/shim`.

### Gerando a Documentação

``` bash
# Gerar documentação para todo o código na pasta app
vendor/bin/phpdoc -d app -t docs/api
```

A documentação gerada estará disponível na pasta `docs/api`.

## Estrutura do Projeto

O projeto segue a arquitetura MVC do Laravel, com algumas camadas adicionais:

- **app/**
    - **DTOs/**: Objetos de Transferência de Dados
    - **Http/Controllers/API/**: Controladores da API
    - **Http/Requests/**: Classes de validação de requisições
    - **Http/Responses/**: Classes para formatação de respostas
    - **Interfaces/**: Interfaces para contratos de serviços e repositórios
    - **Models/**: Modelos Eloquent
    - **Repositories/**: Implementações dos repositórios de dados
    - **Services/**: Implementações dos serviços de negócios

- **database/**
    - **migrations/**: Migrações do banco de dados
    - **seeders/**: Seeds para população do banco de dados

- **routes/**
    - **api.php**: Rotas da API

- **tests/**
    - **Feature/**: Testes de funcionalidades
    - **Http/Controllers/API/**: Testes dos controladores de API
    - **Unit/**: Testes unitários

Para qualquer dúvida adicional, entre em contato com a equipe de desenvolvimento.
