# Desafio Manageasy - Sistema de gerencimento de contatos

## Tecnologias utilizadas

## Back-end
PHP 8.0.30 - Linguagem principal
Composer - Gerenciador de dependências
vlucas/phpdotenv - para gerenciamento de variáveis de ambiente
mysqli + Prepared Statements - Proteção nativa contra SQL Injection
MySQL - Banco de dados relacional para persistência dos dados

## Front-end
HTML, CSS e Bootstrap - para criação e estilização da estrutura do site
JavaScript e Jquey - para manipulações da interface e consumo do back-end

# Estrutra do projeto
Na pasta /src esta contido os arquivos do back-end como: Models, Services e Controllers
para manipulação da API e do Banco de dados assim como arquivos de configuração e conexão com o banco de dados
Já na pasta /public está contido nossos arquivos e pastas do front-end como HTML, CSS e JavaScript
contento toda lógica e estrutura da nossa interface

# Como inicializar o projeto utilizando XAMPP
1° - Extraia os arquivos dentro da pasta htdocs
2° - Execute no terminal dentro da pasta o seguinte comando:
    composer install
3° - Inicie os serviços Apache e MySQL dentro do XAMPP Control Panel
4° - Abra seu MySQL WorkBench e cole o arquivo banco.sql contido dentro da pasta
5° - Crie um arquivo .env no mesmo modelo do .env examplo localizado mais abaixo no documento
6° Acesse http://localhost/desafio-manageasy/public/

# Como deve ser seu arquivo .env
Adicione as seguintes variáveis ao seu .env

DB_HOST= seu host ou localhost
DB_USER= seu usário
DB_PASSWORD= sua senha
DB_PORT= a porta da sua aplicação
DB_NAME= o nome do seu banco de dados

# Rotas para testar a API pelo Insomnia
Dentro da pasta existe um arquivo chamado Insomnia_requests.yaml
Importe-o dentro do seu insomnia e terá acesso as rotas da API: GET, POST, PUT e DELETE