# PistonPit: Sistema para gestão de kartódromo
 
O **PistonPit** é uma plataforma web responsiva desenvolvida para facilitar a operação de agendamentos de horários em um kartódromo. Buscando unificar e facilitar o processo de reserva de horário, agendamento e gestão interna da operação em um só lugar.
 
---
 
## Funcionalidades
 
### Perfis de Acesso
 
* **Administrador** — gerencia funcionários e acessa os relatórios.
* **Financeiro** — abre corridas públicas, bloqueia dias e lança receitas e despesas.
* **Mecânico** — cadastro de karts e lista de compras.
* **Cliente** — se cadastra, reserva corridas e consulta o histórico.
  
### Calendário e Agendamento
 
* Calendário para agendamento de corridas.
* Corridas **privadas** marcadas por cliente para eventos fechados.
* Corridas **públicas** abertas pela administração para eventos públicos.

### Controle Financeiro
 
* Lançamento de receitas e despesas.
* Gráfico mensal de entradas e saídas.

---
 
## Stack Tecnológica
 
* **Back-end:** [PHP](https://www.php.net/) — autenticação, regras das corridas, operações de CRUD e comunicação com o banco.
* **Banco de Dados:** [MySQL](https://www.mysql.com/) — modelo relacional com para o armazenamento de dados da plataforma.
* **Front-end:** [HTML](https://developer.mozilla.org/pt-BR/docs/Web/HTML) e [CSS](https://developer.mozilla.org/pt-BR/docs/Web/CSS) — estrutura das páginas e identidade visual.
* **Interatividade:** [JavaScript](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript) — validação de formulários e comportamento do calendário.

### Infraestrutura

* **[Docker](https://www.docker.com/)** — container para execução da aplicação.
* **[Render](https://render.com/)** — hospedagem da aplicação PHP.
* **[TiDB Cloud](https://www.pingcap.com/tidb-cloud/)** — hospedagem do banco de dados.

---
 
## Arquitetura
 
```text
pistonpit/
├── Dockerfile                  # Container Apache + PHP para o Render
├── render.yaml                 # Configuração do serviço e ambiente
├── db/
│   ├── config.php              # Conexão com o banco de dados
│   ├── setDb.php               # Criação e carga do banco
│   └── pistonpit.sql           # Script das tabelas do sistema
└── src/                        # Raiz pública do Apache
    ├── client_login.php        # Entrada do cliente
    ├── emplo_login.php         # Entrada dos funcionários
    ├── client_cadastro.php     # Cadastro de cliente
    ├── emplo_cadastro.php      # Cadastro de funcionários
    ├── auth.php                # Controle de sessão do cliente
    ├── auth_emplo.php          # Controle de sessão da equipe
    ├── crud.php                # Funções de cadastro, consulta, edição e exclusão
    ├── mainMenu.php            # Área do cliente
    ├── mainEmplo.php           # Área da equipe
    ├── financeiro.php          # Lançamentos financeiros e gráfico mensal
    ├── lista_client.php        # Lista de clientes
    ├── lista_emplo.php         # Lista de funcionários
    ├── perfil_client.php       # Perfil do cliente
    ├── perfil_emplo.php        # Perfil do funcionário
    ├── logout.php              # Encerramento de sessão
    └── css/                    # Estilização da tela
```

## Instrução de instalação local

## Requisitos

* **[XAMPP](https://www.apachefriends.org/)** — traz o Apache e o PHP (versão 8.2 ou superior) já configurados. Serve também o MySQL, caso prefira não instalar o banco separadamente.
* **[MySQL 8](https://dev.mysql.com/downloads/mysql/)** — banco de dados do sistema.
* # PistonPit: Sistema para gestão de kartódromo
 
O **PistonPit** é uma plataforma web responsiva desenvolvida para facilitar a operação de agendamentos de horários em um kartódromo. Buscando unificar e facilitar o processo de reserva de horário, agendamento e gestão interna da operação em um só lugar.
 
---
 
## Funcionalidades
 
### Perfis de Acesso
 
* **Administrador** — gerencia funcionários e acessa os relatórios.
* **Financeiro** — abre corridas públicas, bloqueia dias e lança receitas e despesas.
* **Mecânico** — cadastro de karts e lista de compras.
* **Cliente** — se cadastra, reserva corridas e consulta o histórico.
  
### Calendário e Agendamento
 
* Calendário para agendamento de corridas.
* Corridas **privadas** marcadas por cliente para eventos fechados.
* Corridas **públicas** abertas pela administração para eventos públicos.

### Controle Financeiro
 
* Lançamento de receitas e despesas.
* Gráfico mensal de entradas e saídas.

---
 
## Stack Tecnológica
 
* **Back-end:** [PHP](https://www.php.net/) — autenticação, regras das corridas, operações de CRUD e comunicação com o banco.
* **Banco de Dados:** [MySQL](https://www.mysql.com/) — modelo relacional com para o armazenamento de dados da plataforma.
* **Front-end:** [HTML](https://developer.mozilla.org/pt-BR/docs/Web/HTML) e [CSS](https://developer.mozilla.org/pt-BR/docs/Web/CSS) — estrutura das páginas e identidade visual.
* **Interatividade:** [JavaScript](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript) — validação de formulários e comportamento do calendário.

### Infraestrutura

* **[Docker](https://www.docker.com/)** — container para execução da aplicação.
* **[Render](https://render.com/)** — hospedagem da aplicação PHP.
* **[TiDB Cloud](https://www.pingcap.com/tidb-cloud/)** — hospedagem do banco de dados.

---
 
## Arquitetura
 
```text
pistonpit/
├── Dockerfile                  # Container Apache + PHP 8.2 publicado no Render
├── render.yaml                 # Configuração do serviço e variáveis de ambiente
├── db/
│   ├── config.php              # Conexão com o banco (fora da raiz pública)
│   ├── setDb.php               # Criação e carga inicial da estrutura
│   └── pistonpit.sql           # Script das tabelas do sistema
└── src/                        # Raiz pública do Apache
    ├── client_login.php        # Entrada do cliente (tela inicial do sistema)
    ├── emplo_login.php         # Entrada da equipe interna
    ├── client_cadastro.php     # Cadastro de cliente e termo de consentimento
    ├── emplo_cadastro.php      # Cadastro de funcionários
    ├── auth.php                # Controle de sessão do cliente
    ├── auth_emplo.php          # Controle de sessão da equipe
    ├── crud.php                # Funções de cadastro, consulta, edição e exclusão
    ├── mainMenu.php            # Área do cliente — calendário e reservas
    ├── mainEmplo.php           # Área da equipe — calendário, corridas e bloqueios
    ├── financeiro.php          # Lançamentos e gráfico mensal
    ├── lista_client.php        # Lista de clientes
    ├── lista_emplo.php         # Lista de funcionários
    ├── perfil_client.php       # Perfil e histórico do cliente
    ├── perfil_emplo.php        # Perfil do funcionário
    ├── logout.php              # Encerramento de sessão
    └── css/                    # Folha de estilo por tela (login, menu, calendário, financeiro, perfil)
```

## Instrução de instalação local

## Requisitos

* **[XAMPP](https://www.apachefriends.org/)** — traz o Apache e o PHP (versão 8.2 ou superior) já configurados. Serve também o MySQL, caso prefira não instalar o banco separadamente.
* **[MySQL 8](https://dev.mysql.com/downloads/mysql/)** — banco de dados do sistema.
* **[VS Code](https://code.visualstudio.com/)** — Editor de código com suporte a PHP

## Passo a passo
 
**1.** Clone o repositório dentro da pasta `htdocs` do XAMPP:
 
```bash
git clone https://github.com/victor-fiabane/PistonPit.git
```
 
**2.** Inicie o **Apache** e o **MySQL** no painel de controle do XAMPP.
 
**3.** Abra o phpMyAdmin em `http://localhost/phpmyadmin` ou `MySQL Workbenc` e crie um banco chamado `pistonpit`, com a codificação `utf8mb4`.
 
**4.** Com o banco selecionado, vá na aba **Importar** e envie o arquivo `db/pistonpit.sql`, para criar as tabelas do sistema.
 
**5.** Acesse o sistema no navegador:
 
`http://localhost/PistonPit/src/client_login.php`
 
---
 
## Autor
 
- **Victor Agostinetto Fiabane** — victor.agostinetto.fiabane@rede.ulbra.br(https://code.visualstudio.com/) - Editor de código com suporte a PHP

## Passo a passo
 
**1.** Clone o repositório dentro da pasta `htdocs` do XAMPP:
 
```bash
git clone https://github.com/victor-fiabane/PistonPit.git
```
 
**2.** Inicie o **Apache** e o **MySQL** no painel de controle do XAMPP.
 
**3.** Abra o phpMyAdmin em `http://localhost/phpmyadmin` ou `MySQL Workbenc` e crie um banco chamado `pistonpit`, com a codificação `utf8mb4`.
 
**4.** Com o banco selecionado, vá na aba **Importar** e envie o arquivo `db/pistonpit.sql`, para criar as tabelas do sistema.
 
**5.** Acesse o sistema no navegador:
 
`http://localhost/PistonPit/src/client_login.php`
 
---
 
## Autor
 
- **Victor Agostinetto Fiabane** — victor.agostinetto.fiabane@rede.ulbra.br
