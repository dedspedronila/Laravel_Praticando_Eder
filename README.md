# 🚀 Projeto Laravel - Praticando P3 (Aulas 21 a 31)

Este módulo armazena o progresso prático das **aulas 21 a 31** do [Curso de Laravel do Matheus Battisti - Hora de Codar](https://www.youtube.com/watch?v=5VNriuM1eOs&list=PLnDvRpP8BnewYKI1n2chQrrR4EYiJKbUG&index=31). O deploy foi isolado em uma nova estrutura para preservar o histórico e os arquivos das etapas anteriores.

---

## 🛠️ Tecnologias e Recursos Aplicados

| Recurso | Detalhe |
|---|---|
| **Framework** | Laravel |
| **Ambiente** | AWS EC2 (Ubuntu Server) |
| **Servidor Web** | Apache |
| **CI/CD** | GitHub Actions (SSH + Rsync) |
| **Banco de Dados** | MySQL |

---

## 🚀 Fluxo de Deploy Automatizado

A branch `feature/aula21-31` está configurada com uma pipeline de CI/CD que realiza o deploy automático a cada `git push`. O workflow executa as seguintes etapas:

1. Sincroniza os arquivos locais com `/var/www/html/praticandop3/` na EC2.
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

Como o projeto foi movido para uma pasta nova (`praticandop3`), é necessário preparar o ambiente diretamente no servidor **uma única vez**. Conecte-se via SSH e siga os passos abaixo.

### 1. Criar o diretório e ajustar permissões

```bash
sudo mkdir -p /var/www/html/praticandop3
sudo chown -R ubuntu:www-data /var/www/html/praticandop3
sudo chmod -R 775 /var/www/html/praticandop3
```

### 2. Configurar o arquivo `.env`

O `.env` é excluído pelo deploy por segurança. Crie-o manualmente copiando do projeto anterior e ajuste as variáveis (principalmente o nome do banco):

```bash
cp /var/www/html/praticandop2/.env /var/www/html/praticandop3/.env
nano /var/www/html/praticandop3/.env
```

> ⚠️ Ajuste `DB_DATABASE` para não conflitar com o banco do `praticandop2`.

### 3. Ajustar permissões de escrita (pós-primeiro-deploy)

Assim que o primeiro deploy do GitHub Actions concluir com sucesso:

```bash
cd /var/www/html/praticandop3
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Apontar o Apache para a nova pasta

Edite o Virtual Host:

```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Altere o `DocumentRoot` para:

```
DocumentRoot /var/www/html/praticandop3/public
```

Salve (`Ctrl+O` → `Enter` → `Ctrl+X`) e reinicie o Apache:

```bash
sudo systemctl restart apache2
```


---

## 📌 Próximos Passos

```bash
# Após qualquer alteração local:
git add .
git commit -m "feat: descrição da funcionalidade"
git push
```

O GitHub Actions cuida do resto — sincronização, dependências e cache. 🚀
