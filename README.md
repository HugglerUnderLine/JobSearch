# CodeIgniter 4 REST API

## 1. Projeto

Este projeto é uma **API RESTful desenvolvida em CodeIgniter 4**, projetada para atender aos requisitos da disciplina **Tecnologia Cliente-Servidor**, ministrada na UTFPR - PG.
O objetivo do projeto consiste no desenvolvimento de APIs RESTful, com suporte a **integração com front-ends externos, autenticação via tokens JWT**. A aplicação deve permitir a comunicação com quaisquer frameworks, linguagens ou ferramentas utilizadas por outros alunos, seguindo os conceitos de REST para envio e recuperação de dados em demais sistemas, garantindo as devidas validações de usuários e permissões. Conforme decidido através de votações, o presente sistema visa a implementação de uma plataforma de busca e oferta de empregos.

## Roadmap
### Primeira Entrega Parcial - Cliente
- [ X ] **Enviar dados de cadastro de usuário (comum) para o servidor.**
- [ X ] **Enviar dados de login para o servidor.**
- [ X ] **Pedir dados de cadastro do usuário comum para o servidor.**
- [ X ] **Enviar atualização dos dados do usuário comum para o servidor.**
- [ X ] **Enviar pedido para apagar cadastro de usuário comum.**
- [ X ] **Enviar dados de logout para o servidor.**

### Primeira Entrega Parcial - Servidor
- [ X ] **Processar corretamente dados recebidos de cadastro de usuário (comum).**
- [ X ] **Processar corretamente dados de login.**
- [ X ] **Enviar dados de cadastro do usuário comum para o cliente.**
- [ X ] **Processar corretamente a atualização dos dados do usuário comum.**
- [ X ] **Apagar cadastro de usuário comum.**
- [ X ] **Processar corretamente pedido de logout.**


### Segunda Entrega Parcial - Cliente
- [   ] **A definir.**

### Segunda Entrega Parcial - Servidor
- [   ] **A definir.**


### Terceira Entrega Parcial - Cliente
- [   ] **A definir.**

### Terceira Entrega Parcial - Servidor
- [   ] **A definir.**

---

## 2. Instalação

### Dependências

Certifique-se de ter os seguintes componentes instalados no ambiente:
 ___________________________________________________________________________________________________________
| Componente                    | Versão Recomendada                          | Observações                 |
|-------------------------------|---------------------------------------------|-----------------------------|
| **PHP**                       | 8.1 ou superior                             | Recomendado PHP 8.3         |
| **Composer**                  | 2.x                                         | Gerenciador de dependências |
| **Banco de Dados**            | PostgreSQL 15 ou superior                   | Ajuste o driver no `.env`   |
| **Extensões PHP necessárias** | `intl`, `mbstring`, `json`, `curl`, `pgsql` | Verifique com `php -m`      |
 -----------------------------------------------------------------------------------------------------------

---

### Configurações do `php.ini`

Abra seu arquivo `php.ini` e confirme que as seguintes extensões estão habilitadas:

```ini
extension=intl
extension=mbstring
extension=json
extension=curl
extension=pdo_pgsql
extension=pgsql
```

Reinicie o servidor PHP após qualquer modificação no arquivo `php.ini`.

---

### Instalação do Projeto

Clone o repositório e instale as dependências com o Composer:

```bash
git clone https://github.com/HugglerUnderLine/JobSearch.git
cd JobSearch
composer install
```

Caso o Composer não esteja configurado globalmente:

```bash
php /usr/local/bin/composer install
```

---

## 3. Configuração do `.env`

Crie o arquivo `.env` a partir do modelo existente:

```bash
cp env .env
```

Edite o arquivo `.env` e ajuste conforme necessário:

```dotenv
CI_ENVIRONMENT = development

app.baseURL = 'http://SEU_IP_OU_LOCALHOST:8080/'

database.default.hostname = localhost
database.default.database = JobSearch
database.default.username = usuario
database.default.password = senha
database.default.DBDriver = Postgre
database.default.port = 5432
```

> 🔹 **Substitua** `SEU_IP_OU_LOCALHOST` pelo IP real da máquina onde o servidor PHP será executado.  
> Exemplo: `http://192.168.0.10:8080/` ou `http://localhost:8080/`.

---

## 4. Criando o Banco de Dados

O CodeIgniter utiliza o sistema de **migrations** para criar e atualizar tabelas automaticamente.
Este projeto foi pensado para utilização conjunta com o PostgreSQL. Para evitar erros ou incompatibilidade de consultas, é altamente recomendado que o modelo utilizado seja mantido.
Durante a instalação do PostgreSQL, certifique-se de instalar o servidor local para permitir que a conexão seja feita sem a necessidade de inicializar e encerrar o servidor manualmente.

Antes de tudo, criamos a base de dados no seu ambiente PostgreSQL:

```bash
CREATE DATABASE JobSearch;
```

Se você já configurou corretamente as credenciais de acesso ao banco de dados no `.env`, execute:

```bash
php spark migrate
```

Caso queira recriar todas as tabelas do zero:

```bash
php spark migrate:refresh
```

Para listar todas as migrações aplicadas:

```bash
php spark migrate:status
```

---

## 5. Inicializando o Servidor

Para iniciar o servidor embutido do CodeIgniter:

```bash
php spark serve --host 0.0.0.0 --port 8080
```

A aplicação ficará disponível em:

```
http://SEU_IP:8080/
```

> 🔹 `--host 0.0.0.0` permite que outros dispositivos da rede local acessem o projeto.  
> 🔹 `--port` pode ser ajustada conforme necessidade.

---

## Dicas de Uso

- Execute `php spark routes` para listar todas as rotas disponíveis.
- Os logs da aplicação estão em `writable/logs/`.

---

## $end

Com essas configurações, o projeto estará pronto para ser executado localmente ou em ambiente de rede.

---

## Info
**Desenvolvido por:** Vitor Huggler
**Stack: PHP 8.3 · CodeIgniter 4 · PostgreSQL · REST API**
