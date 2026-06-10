# 🚀 Projeto Laravel - Praticando P3 (Aulas 21 a 31)

Este módulo armazena o progresso prático das **aulas 21 a 31** do [Curso de Laravel do Matheus Battisti - Hora de Codar](https://www.youtube.com/watch?v=5VNriuM1eOs&list=PLnDvRpP8BnewYKI1n2chQrrR4EYiJKbUG&index=31). O deploy foi isolado em uma nova estrutura para preservar o histórico e os arquivos das etapas anteriores.

---

## 🛠️ Tecnologias e Recursos Aplicados

| Recurso | Detalhe |
|---|---|
| **Framework** | Laravel + Jetstream |
| **Ambiente** | AWS EC2 (Ubuntu Server) |
| **Servidor Web** | Apache2 |
| **CI/CD** | GitHub Actions (SSH + Rsync) |
| **Banco de Dados** | MySQL |
| **Frontend** | Vite + Tailwind CSS + Livewire |

---

## 🚀 Fluxo de Deploy Automatizado

A branch `feature/aula21-31` está configurada com uma pipeline de CI/CD que realiza o deploy automático a cada `git push`. O workflow executa as seguintes etapas:

1. Sincroniza os arquivos locais com `/var/www/html/praticandop3/` na EC2 via Rsync.
2. Ignora `/vendor/`, `/node_modules/` e o arquivo `.env`.
3. Executa comandos remotos via SSH: `composer install`, `migrate` e limpeza de cache do Laravel.

### Como atualizar o servidor no dia a dia

```bash
git add .
git commit -m "feat: descrição da nova funcionalidade da aula"
git push
```

---

## 🎛️ Configuração Inicial Obrigatória na EC2

Necessária **apenas uma vez** após mover o projeto para a pasta `praticandop3`. Conecte-se via SSH e siga os passos em ordem.

### Pré-requisitos do sistema

Garanta que o servidor possui os seguintes pacotes instalados:

- **PHP 8.3+** com extensões: `php-mbstring`, `php-xml`, `php-bcmath`, `php-curl`, `php-zip`, `php-mysql`
- **Apache2**
- **MySQL Server**
- **Composer**

---

### 1. Criar o diretório e ajustar permissões

```bash
sudo mkdir -p /var/www/html/praticandop3
sudo chown -R ubuntu:www-data /var/www/html/praticandop3
sudo chmod -R 775 /var/www/html/praticandop3
```

---

### 2. Configurar o arquivo `.env`

O `.env` é excluído pelo deploy por motivos de segurança. Crie-o manualmente copiando do projeto anterior e ajuste as variáveis:

```bash
cp /var/www/html/praticandop2/.env /var/www/html/praticandop3/.env
nano /var/www/html/praticandop3/.env
```

> ⚠️ Ajuste `DB_DATABASE` para não conflitar com o banco do `praticandop2`.

Em seguida, gere a chave de criptografia do Laravel (obrigatório para o Jetstream):

```bash
php artisan key:generate
```

---

### 3. Instalar dependências do PHP e cachear configurações

```bash
composer install --no-dev --optimize-autoloader

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 4. Configurar o Node.js via NVM (necessário para o Vite)

O Vite exige uma versão atualizada do Node.js para compilar os assets do Jetstream (Tailwind + Livewire). Use o NVM para garantir a versão correta:

```bash
# Instalar o NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash

# Carregar o NVM no terminal atual
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Instalar e ativar o Node.js 22
nvm install 22
nvm use 22
```

---

### 5. Compilar os assets com o Vite

Com a versão correta do Node ativa, instale as dependências de frontend e gere o manifesto de produção:

```bash
# Limpar node_modules antigos se existirem
rm -rf node_modules

npm install
npm run build
```

---

### 6. Preparar a pasta de uploads

O Laravel precisa do diretório abaixo para salvar as imagens dos eventos:

```bash
mkdir -p public/img/events
```

---

### 7. Ajustar permissões de escrita (Apache)

Para evitar erros HTTP 500, garanta que o `www-data` e o usuário `ubuntu` tenham acesso total às pastas críticas:

```bash
sudo chown -R ubuntu:www-data .
sudo chmod -R 775 storage bootstrap/cache public/build public/img
```

---

### 8. Configurar o banco de dados MySQL

Acesse o MySQL e certifique-se de que o banco configurado no `.env` existe:

```sql
CREATE DATABASE IF NOT EXISTS seu_banco_aqui;
```

Em seguida, rode as migrations para criar toda a estrutura de tabelas:

```bash
php artisan migrate:fresh
```

---

### 9. Configurar o Virtual Host do Apache

Edite o arquivo de configuração:

```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

O bloco deve ficar assim (o `AllowOverride All` é essencial para as rotas do Laravel não retornarem 404):

```apache
DocumentRoot /var/www/html/praticandop3/public

<Directory /var/www/html/praticandop3/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Salve (`Ctrl+O` → `Enter` → `Ctrl+X`), ative o módulo rewrite e reinicie o Apache:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```


---

## 📌 Rotina de atualização

```bash
# Após qualquer alteração local:
git add .
git commit -m "feat: descrição da funcionalidade"
git push
```

O GitHub Actions cuida do resto — sincronização, dependências e cache. 🚀
