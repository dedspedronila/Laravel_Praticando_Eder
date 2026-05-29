

# 🚀 Deploy Laravel na AWS EC2 com CI/CD via GitHub Actions

Guia completo do zero ao deploy automático. Qualquer `git push` na branch configurada atualiza o servidor de produção sozinho.

## 📋 Índice

* [Requisitos do Ambiente](https://www.google.com/search?q=%23-requisitos-do-ambiente)
* [Passo 1 — Preparando a EC2](https://www.google.com/search?q=%23-passo-1--preparando-a-ec2)
* [Passo 2 — Configurando o MySQL](https://www.google.com/search?q=%23-passo-2--configurando-o-mysql)
* [Passo 3 — Configurando o Apache](https://www.google.com/search?q=%23-passo-3--configurando-o-apache)
* [Passo 4 — Configurando o GitHub Actions](https://www.google.com/search?q=%23-passo-4--configurando-o-github-actions)
* [Passo 5 — Inicialização do Projeto na EC2](https://www.google.com/search?q=%23-passo-5--inicializa%C3%A7%C3%A3o-do-projeto-na-ec2)
* [Trabalhando de uma Nova Máquina / Criando seu próprio Repositório](https://www.google.com/search?q=%23-trabalhando-de-uma-nova-m%C3%A1quina-local--criando-seu-pr%C3%B3prio-reposit%C3%B3rio)

---

## 🛠️ Requisitos do Ambiente

| Componente | Versão |
| --- | --- |
| **Servidor Web** | Apache 2 (com módulo `rewrite` ativo) |
| **Linguagem** | PHP 8.x + extensões do Laravel |
| **Gerenciador de Dependências** | Composer |
| **Banco de Dados** | MySQL Server 8.0 |
| **Interface de Banco (opcional)** | phpMyAdmin |

---

## 🏗️ Passo 1 — Preparando a Instância EC2

Após criar a instância Ubuntu na AWS e conectar via SSH, rode os comandos abaixo para atualizar o sistema e instalar todos os pacotes necessários:

```bash
# Atualizar a lista de pacotes
sudo apt update && sudo apt upgrade -y

# Instalar Apache, PHP e extensões exigidas pelo Laravel
sudo apt install apache2 php libapache2-mod-php php-mbstring php-xml php-bcmath php-json php-mysql php-curl php-zip unzip -y

# Instalar o Composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar o MySQL Server
sudo apt install mysql-server -y

```

---

## 🗄️ Passo 2 — Configurando o Banco de Dados (MySQL)

Por padrão, o MySQL no Ubuntu não tem senha definida para o root. Configure-o para que o Laravel consiga se conectar:

1. Acesse o terminal do MySQL:
```bash
sudo mysql

```


2. Defina a senha do root e crie o banco da aplicação:
```sql
-- Troque 'sua_senha_aqui' pela senha que usará no .env
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'sua_senha_aqui';

-- Crie o banco de dados da aplicação
CREATE DATABASE nome_do_seu_banco;

FLUSH PRIVILEGES;
EXIT;

```



---

## 🌐 Passo 3 — Configurando o Apache

O Apache precisa apontar para a pasta `public` do Laravel e ter permissão para processá-la.

### 3.1 — Editar o VirtualHost

```bash
sudo nano /etc/apache2/sites-available/000-default.conf

```

Substitua o conteúdo para apontar para a pasta `public` do seu projeto:

```text
ServerAdmin webmaster@localhost
DocumentRoot /var/www/html/aula11-20/public

<Directory /var/www/html/aula11-20/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

```

> 💡 **Dica:** Para salvar no Nano, pressione `Ctrl + O` → `Enter`. Para sair: `Ctrl + X`.

### 3.2 — Ativar módulos e reiniciar o Apache

```bash
# Ativar o módulo rewrite (essencial para as rotas do Laravel)
sudo a2enmod rewrite

# Reiniciar o Apache para aplicar as alterações
sudo systemctl restart apache2

```

---

## 🤖 Passo 4 — Configurando o GitHub Actions

O CI/CD envia os arquivos automaticamente para a EC2 via SSH a cada `git push`.

### 4.1 — Criar o arquivo de Workflow

Na raiz do projeto local, crie a pasta `.github/workflows/` e o arquivo `deploy.yml` com o seguinte conteúdo:

```yaml
name: Push-to-EC2

on:
  push:
    branches:
      - feature/aula11-20  # 👈 Troque pelo nome da sua branch

jobs:
  deploy:
    name: Deploy to EC2
    runs-on: ubuntu-latest

    steps:
      - name: Checkout the files
        uses: actions/checkout@v4

      - name: Copy files with SSH
        uses: easingthemes/ssh-deploy@main
        env:
          SSH_PRIVATE_KEY: ${{ secrets.EC2_SSH_KEY }}
          ARGS: "-rltgoDzvO"
          SOURCE: "./"
          REMOTE_HOST: "34.235.166.45"          # 👈 IP Público atual da sua EC2
          REMOTE_USER: "ubuntu"
          TARGET: "/var/www/html/aula11-20/"    # 👈 Pasta de destino na EC2
          EXCLUDE: "/vendor/, /node_modules/, .env, /storage/logs/, /storage/framework/"

```

> ⚠️ **Lembre:** Você deve trocar o IP da EC2 toda vez que a instância for reiniciada (se não tiver um Elastic IP associado).

### 4.2 — Cadastrar a Chave SSH no GitHub

Para o GitHub Actions acessar a EC2 sem pedir senha por fora do código:

1. No repositório do GitHub, vá em **Settings** → **Secrets and variables** → **Actions**.
2. Clique em **New repository secret**.
3. Preencha:
* **Name:** `EC2_SSH_KEY`
* **Value:** Cole todo o conteúdo do seu arquivo `.pem` (incluindo as linhas `-----BEGIN...` e `-----END...`).



---

## ⚡ Passo 5 — Inicialização do Projeto na EC2 (Primeira vez)

Após o primeiro `git push`, o GitHub Actions cria a pasta do projeto na EC2. Porém, a pasta `vendor` e o arquivo `.env` não sobem pelo Git por segurança — você precisa configurá-los apenas uma vez por pasta:

```bash
# 1. Entrar na pasta criada pelo deploy
cd /var/www/html/aula11-20

# 2. Instalar as dependências PHP de produção
composer install --no-dev

# 3. Criar o arquivo .env e gerar a chave de segurança do Laravel
cp .env.example .env
php artisan key:generate

# 4. Criar as pastas internas de cache (que ficam no .gitignore)
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views

# 5. Ajustar permissões para o Apache (www-data) conseguir ler o projeto
sudo chown -R ubuntu:www-data /var/www/html/aula11-20
sudo chmod -R 755 /var/www/html/aula11-20
sudo chmod -R 775 storage bootstrap/cache public/img

```

> Após esses passos, edite o arquivo `.env` com as credenciais do banco de dados criadas no Passo 2.

---

## 💻 Trabalhando de uma Nova Máquina Local / Criando seu próprio Repositório

Se você quer usar este repositório como **molde base** para o seu próprio trabalho, cloná-lo em sua máquina local e enviá-lo para uma **nova conta ou repositório privado próprio no GitHub**, siga este fluxo:

### 1. Clonar o projeto base direto na branch correta

Abra o terminal do seu computador e baixe a branch de desenvolvimento original:

```bash
git clone -b feature/aula11-20 https://github.com/dedspedronila/Laravel_Praticando_Eder.git
cd Laravel_Praticando_Eder

```

### 2. Alterar a propriedade do repositório remoto (Desvincular do original)

Crie um repositório **vazio** na sua conta pessoal do GitHub. Em seguida, mude a URL de destino dos seus commits locais para apontar para esse seu novo repositório:

```bash
# Substitua pela URL do repositório vazio criado na SUA conta
git remote set-url origin https://github.com/SEU_USUARIO_DO_GITHUB/SEU_NOVO_REPOSITORIO.git

```

### 3. Inicializar o ambiente local na sua máquina

Como pastas pesadas e arquivos de configuração ficam fora do Git, configure a sua máquina local antes de começar a codificar:

```bash
# Instalar os pacotes necessários na sua máquina
composer install

# Configurar o ambiente local do Laravel
cp .env.example .env
php artisan key:generate

```

### 4. Fazer o envio para o SEU repositório novo

A partir daqui, o seu projeto está totalmente desvinculado do original e protegido na sua conta. Envie o código para o seu repositório remoto:

```bash
git push -u origin feature/aula11-20

```

> ⚙️ **Nota sobre o Deploy:** Se você deseja que a automação via GitHub Actions passe a atualizar a **sua** instância EC2 particular, lembre-se de atualizar o campo `REMOTE_HOST` no arquivo `.github/workflows/deploy.yml` com o seu IP público e cadastrar o segredo `EC2_SSH_KEY` nas configurações do seu novo repositório.

---

## 🔄 Fluxo Resumido

Código local → `git push` → GitHub Actions → SSH → EC2 (Apache + Laravel)

## 📝 Observações

* O arquivo `.env` nunca deve ser commitado no repositório.
* A pasta `vendor/` também fica fora do Git — o `composer install` na EC2 é necessário apenas na primeira vez que a pasta do projeto é criada.
* Se o IP da sua EC2 mudar, atualize o campo `REMOTE_HOST` no `deploy.yml` e faça um novo push.
* Para um ambiente de produção real, considere associar um Elastic IP à instância para evitar a troca de IP a cada reinício.
