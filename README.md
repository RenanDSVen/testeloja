# Sistema de Loja de Roupas

Aplicação para uma loja física, desenvolvida em PHP 8.2, MySQL 8, PDO e interface responsiva.

## Recursos incluídos

- Instalação pelo navegador e criação segura do administrador
- Usuários administrador, caixa e estoque
- Produtos com cor, tamanho, SKU e código de barras único
- Consulta instantânea de código duplicado
- Custo, lucro percentual e preço de venda
- Estoque e histórico de movimentações
- Clientes, fornecedores e limite de crédito
- PDV preparado para leitor USB
- Caixa, sangria, suprimento e fechamento
- Crediário simples e recebimento parcial
- Contas a pagar, vendas e relatórios de lucro
- Configurações e auditoria

## Instalação

1. Baixe e extraia o repositório dentro do Apache.
2. Edite `config/database.php` se o MySQL não usar `root` sem senha.
3. Acesse `http://localhost/testeloja/install.php`.
4. Crie o primeiro administrador.
5. Entre no sistema.

O banco padrão é `loja_roupas`. Para alterar o nome exibido antes da instalação, edite `config/app.php`; depois, use Configurações no sistema.

## Requisitos

- PHP 8.2 ou superior
- Extensões PDO e pdo_mysql
- MySQL 8
- Apache com permissão de escrita na pasta `storage`

## Leitor de código de barras

Leitores USB em modo teclado funcionam diretamente. No PDV, posicione o cursor no campo Código de barras ou SKU e faça a leitura.
