# HDC Events — Laravel (Vídeos #1 ao #10)

Projeto desenvolvido seguindo a série do **Matheus Battisti** no YouTube. Este guia cobre tudo que você precisa para rodar a aplicação no seu computador e validar os checkpoints com o **Prof. Dr. Eder Pansani**.

---

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- [PHP 8+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [XAMPP](https://www.apachefriends.org/) (ou outro servidor MySQL local)
- [Git](https://git-scm.com/)

---

## 1. Clonar o Repositório

Abra o terminal na pasta de destino (ex: `/var/www/html/AulaWEB/VTPDWE2/`) e execute:

```bash
git clone https://github.com/dedspedronila/Laravel_Praticando_Eder.git
cd Laravel_Praticando_Eder
```

---

## 2. Instalar as Dependências

A pasta `vendor` não vai para o repositório (está no `.gitignore`), então você precisa baixá-la localmente:

```bash
composer install
```

---

## 3. Configurar o `.env`

Copie o arquivo de exemplo para criar o seu `.env`:

```bash
# Linux/Mac
cp .env.example .env

# Windows (Prompt de Comando)
copy .env.example .env
```

Abra o `.env` gerado e ajuste o bloco do banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hdcevents
DB_USERNAME=root
DB_PASSWORD=
```

> **Atenção (XAMPP):** Se você usa o XAMPP padrão, deixe `DB_PASSWORD=` em branco mesmo — sem nada após o `=`.

---

## 4. Gerar a Chave da Aplicação

Esse comando preenche o `APP_KEY` no `.env`. Sem ele, o Laravel retorna um **Erro HTTP 500**:

```bash
php artisan key:generate
```

---

## 5. Criar o Banco de Dados e Rodar as Migrations

1. Acesse o **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Crie um banco de dados vazio chamado exatamente `hdcevents`
   - Collation recomendada: `utf8mb4_unicode_ci`
3. Volte ao terminal e execute:

```bash
php artisan migrate
```

### ✅ Validação exigida pelo professor (pág. 30 do roteiro)

Execute o comando **uma segunda vez**. O resultado esperado é:

```
INFO  Nothing to migrate.
```

Esse output confirma que a conexão com o banco está funcionando corretamente e todas as migrations já foram aplicadas.

---

## 6. Iniciar o Servidor

```bash
php artisan serve
```

A aplicação ficará disponível em `http://127.0.0.1:8000`.

---

## 7. Links para os Checkpoints

Deixe estas abas abertas no navegador durante a avaliação:

| Aula(s) | Descrição | URL |
|---|---|---|
| #3 | Rotas e Views simples | `http://127.0.0.1:8000/contact` |
| #4, #5, #6 | Loops, Parâmetros e Banner | `http://127.0.0.1:8000/` |
| #8 | Filtro dinâmico de busca (Query String) | `http://127.0.0.1:8000/produtos?search=camisa` |
| #8 | Parâmetro dinâmico de URL | `http://127.0.0.1:8000/produtos_teste/5` |
| #9 | Layout Global + Menu + Controller | `http://127.0.0.1:8000/events/create` |

---

## Resumo dos Comandos

```bash
git clone https://github.com/dedspedronila/Laravel_Praticando_Eder.git
cd Laravel_Praticando_Eder
composer install
cp .env.example .env   # ou: copy .env.example .env (Windows)
# configure DB_DATABASE, DB_USERNAME e DB_PASSWORD no .env
php artisan key:generate
php artisan migrate
php artisan serve
```
