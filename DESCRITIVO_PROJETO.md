# Descritivo do Projeto - Nexus Core

## 1. Identificacao

**Nome do aluno:** Wesley  
**Nome do projeto:** Nexus Core  
**Link do GitHub:** https://github.com/Wesley203226/Nexus-core  
**Stack utilizada:** Laravel 12, PHP 8.2, Vue 3, Vite, Tailwind CSS, Axios, Chart.js, MySQL e API JSON.

## 2. Tema e proposta do sistema

O Nexus Core e um sistema web de gerenciamento de estoque e catalogo operacional. A proposta e permitir que uma empresa organize seus produtos, tipos/categorias e fornecedores em uma interface administrativa moderna, com indicadores de estoque, filtros, cadastros completos e envio de imagens.

O sistema foi pensado para evoluir como uma plataforma interna de controle de inventario, podendo receber autenticacao, perfis de usuario, area administrativa protegida, relatorios, graficos e novas funcionalidades relacionadas a compras, vendas ou movimentacoes de estoque.

## 3. Sistema/app/site de inspiracao

O projeto se inspira em sistemas de gestao de estoque e ERPs simplificados, como Bling, Tiny ERP, Odoo Inventory e dashboards administrativos usados por lojas virtuais. A ideia nao e copiar uma ferramenta especifica, mas aplicar o conceito de painel operacional com dados resumidos, listagens filtraveis e CRUDs integrados.

## 4. Publico-alvo

O publico-alvo sao pequenas empresas, lojas, equipes administrativas, almoxarifados e gestores que precisam controlar produtos, fornecedores e categorias de forma simples. O sistema tambem pode ser usado como base para negocios que desejam sair de planilhas e centralizar as informacoes em um banco de dados relacional.

## 5. Justificativa da ideia

A escolha do tema se justifica porque o controle de estoque e uma necessidade comum em varios tipos de negocio. Um sistema desse tipo permite aplicar os principais conteudos da disciplina em Laravel, como rotas, controllers, models, migrations, relacionamento entre tabelas, validacao, CRUD, API JSON, upload de arquivos e integracao com uma interface web.

O projeto possui complexidade suficiente para evoluir, mas continua organizado em um escopo viavel: produtos, tipos e fornecedores. A partir dessa base, e possivel incluir login, permissoes, relatorios, graficos, historico de movimentacao e integracao com outros sistemas.

## 6. Lista inicial de requisitos funcionais

- Cadastrar, listar, editar e remover produtos.
- Cadastrar, listar, editar e remover tipos/categorias de produtos.
- Cadastrar, listar, editar e remover fornecedores.
- Vincular cada produto a um tipo.
- Vincular opcionalmente cada produto a um fornecedor.
- Enviar foto para produtos.
- Enviar foto de perfil para fornecedores.
- Filtrar produtos por busca textual, tipo e fornecedor.
- Filtrar fornecedores por busca textual e status.
- Filtrar tipos por busca textual.
- Consultar produtos em uma area publica de catalogo.
- Exibir indicadores no dashboard, como total de itens, valor estimado do estoque, fornecedores ativos e tipos cadastrados.
- Identificar itens com estoque baixo.
- Bloquear a exclusao de tipos que ainda possuem produtos vinculados.
- Expor endpoints JSON para produtos, tipos e fornecedores.
- Validar dados obrigatorios antes de salvar cadastros.

## 7. Lista de tabelas

As principais tabelas previstas/utilizadas no banco de dados sao:

- `users`
- `products`
- `types`
- `suppliers`
- `password_reset_tokens`
- `sessions`
- `cache`
- `jobs`

As tabelas centrais do dominio do projeto sao `products`, `types` e `suppliers`. A tabela `users` esta prevista pela estrutura padrao do Laravel e sera usada na evolucao com login/autenticacao.

## 8. Campos basicos das tabelas

### Tabela: `users`

Campos principais:

- `id`
- `name`
- `email`
- `email_verified_at`
- `password`
- `remember_token`
- `created_at`
- `updated_at`

Finalidade: armazenar usuarios do sistema para uma futura area administrativa com login.

### Tabela: `types`

Campos principais:

- `id`
- `name`
- `description`
- `created_at`
- `updated_at`

Finalidade: representar os tipos ou categorias usadas para organizar os produtos.

### Tabela: `suppliers`

Campos principais:

- `id`
- `name`
- `contact_name`
- `email`
- `phone`
- `document`
- `is_active`
- `notes`
- `profile_photo_path`
- `created_at`
- `updated_at`

Finalidade: armazenar fornecedores, seus dados de contato, status e foto de perfil.

### Tabela: `products`

Campos principais:

- `id`
- `name`
- `description`
- `quantity`
- `price`
- `type_id`
- `supplier_id`
- `photo_path`
- `created_at`
- `updated_at`

Finalidade: armazenar os produtos/itens do estoque, incluindo quantidade, preco, foto e relacionamentos.

### Tabela: `password_reset_tokens`

Campos principais:

- `email`
- `token`
- `created_at`

Finalidade: suporte futuro para recuperacao de senha.

### Tabela: `sessions`

Campos principais:

- `id`
- `user_id`
- `ip_address`
- `user_agent`
- `payload`
- `last_activity`

Finalidade: armazenar sessoes dos usuarios quando a autenticacao for ativada.

## 9. Relacionamentos entre tabelas

- Um produto pertence a um tipo.
- Um tipo possui varios produtos.
- Um produto pode pertencer a um fornecedor.
- Um fornecedor possui varios produtos.
- Um usuario podera acessar a area administrativa do sistema quando a autenticacao for implementada.

Em termos tecnicos:

- `products.type_id` referencia `types.id`.
- `products.supplier_id` referencia `suppliers.id`.
- Ao remover um fornecedor, os produtos vinculados podem ficar sem fornecedor.
- A remocao de um tipo e bloqueada quando existem produtos vinculados a ele.

## 10. Lista de telas previstas

### Area publica

Na versao atual, o sistema funciona como uma SPA carregada pelo Laravel. A area publica pode evoluir para:

- Pagina inicial publica do catalogo.
- Visualizacao publica de produtos.
- Detalhes de produto.
- Filtros por tipo/categoria.

### Area administrativa

Telas implementadas:

- Dashboard / visao geral com graficos.
- Listagem e cadastro de produtos (CRUD completo).
- Listagem e cadastro de fornecedores (CRUD completo).
- Listagem e cadastro de tipos/categorias (CRUD completo).
- Relatorios de estoque consolidados.
- Upload de imagens para produtos e fornecedores.
- Login administrativo (estrutura preparada, SPA integrada).

## 11. Casos de uso e fluxos do sistema

### Usuario visitante

Fluxo previsto para uma evolucao publica do sistema:

1. Acessa a pagina inicial.
2. Visualiza os produtos disponiveis no catalogo.
3. Filtra produtos por tipo/categoria.
4. Abre os detalhes de um produto.

### Administrador

Fluxo atual e previsto:

1. Acessa o sistema.
2. Visualiza o dashboard com indicadores gerais.
3. Consulta itens cadastrados e verifica alertas de estoque baixo.
4. Cadastra tipos/categorias para organizar o estoque.
5. Cadastra fornecedores com dados de contato, status e foto.
6. Cadastra produtos com nome, descricao, quantidade, preco, tipo, fornecedor e foto.
7. Edita produtos quando houver alteracao de preco, quantidade, descricao ou imagem.
8. Remove produtos que nao fazem mais parte do estoque.
9. Remove ou atualiza fornecedores quando necessario.
10. Acessa relatorios e analisa graficos para acompanhar a situacao do estoque.

### Fluxo de cadastro de produto

1. O administrador acessa a tela de itens.
2. Clica em "Novo item".
3. Preenche nome, preco, quantidade, tipo e descricao.
4. Seleciona um fornecedor, se existir.
5. Envia uma foto do produto, se desejar.
6. Salva o cadastro.
7. O sistema valida os dados e registra o produto no banco.
8. O produto passa a aparecer na listagem e nos indicadores do dashboard.

### Fluxo de cadastro de fornecedor

1. O administrador acessa a tela de fornecedores.
2. Clica em "Novo fornecedor".
3. Informa nome, responsavel, email, telefone, documento, status e observacoes.
4. Envia uma foto de perfil, se desejar.
5. Salva o fornecedor.
6. O fornecedor fica disponivel para ser vinculado aos produtos.

### Fluxo de cadastro de tipo/categoria

1. O administrador acessa a tela de tipos.
2. Clica em "Novo tipo".
3. Informa nome e descricao.
4. Salva o tipo.
5. O tipo fica disponivel para classificar produtos.
6. Caso o tipo tenha produtos vinculados, o sistema impede sua exclusao para preservar a integridade dos dados.

## 12. API JSON

O projeto possui rotas de API para os principais recursos:

- `GET /api/products` - lista produtos.
- `POST /api/products` - cadastra produto.
- `GET /api/products/{id}` - exibe produto especifico.
- `PUT/PATCH /api/products/{id}` - atualiza produto.
- `DELETE /api/products/{id}` - remove produto.
- `GET /api/types` - lista tipos.
- `POST /api/types` - cadastra tipo.
- `GET /api/types/{id}` - exibe tipo especifico.
- `PUT/PATCH /api/types/{id}` - atualiza tipo.
- `DELETE /api/types/{id}` - remove tipo.
- `GET /api/suppliers` - lista fornecedores.
- `POST /api/suppliers` - cadastra fornecedor.
- `GET /api/suppliers/{id}` - exibe fornecedor especifico.
- `PUT/PATCH /api/suppliers/{id}` - atualiza fornecedor.
- `DELETE /api/suppliers/{id}` - remove fornecedor.

## 13. Observacoes sobre implementacao

- O sistema usa Laravel no backend e Vue 3 no frontend.
- A interface e renderizada como SPA usando Vue Router.
- O backend fornece os dados por meio de API JSON.
- Os cadastros possuem validacao nos controllers.
- O projeto possui upload de imagens para produtos e fornecedores.
- O dashboard ja apresenta indicadores operacionais e graficos.
- O sistema possui relatorio consolidado de estoque por tipo, fornecedor e produtos com estoque baixo.
- Existem testes automatizados cobrindo os CRUDs de inventario.
- A autenticacao ainda e uma evolucao prevista, mas a estrutura `users`, `sessions` e rotas protegidas do Laravel permitem essa expansao.
- O banco configurado no exemplo utiliza MySQL com a base `lojavirtual`.

## 14. Possiveis evolucoes futuras

- Implementar login e logout com Laravel Breeze, Sanctum ou outra solucao da stack Laravel para protecao real das rotas.
- Criar perfis de usuario com diferentes niveis de acesso (ex: admin, operador).
- Adicionar movimentacoes de estoque detalhadas (entradas e saidas com historico).
- Exportar relatorios em PDF ou CSV.
- Implementar notificações por e-mail quando o estoque estiver muito baixo.

## 15. Conclusao

O Nexus Core atende a proposta da disciplina por ser um sistema web baseado em Laravel com banco de dados relacional, CRUDs, relacionamentos, API JSON e interface administrativa. O projeto possui uma base consistente para evoluir com autenticacao, dashboard com graficos, relatorios e area publica, mantendo um escopo coerente com um sistema real de gerenciamento de estoque.
