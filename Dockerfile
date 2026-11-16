# ===================================================================
# Dockerfile - PistonPit no Render
# O Render nao tem runtime PHP nativo, so Docker. Este arquivo monta
# um container com Apache + PHP 8.2 e joga o projeto dentro dele.
# Coloque este arquivo na RAIZ do repositorio (junto do index.php).
# ===================================================================
FROM php:8.2-apache

# Certificados usados para conectar no banco online (TiDB) com seguranca
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates \
    && update-ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Extensoes do PHP: pdo_mysql e o que o setDb.php usa
RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite headers

# Copia o codigo do projeto para a pasta publica do Apache
COPY . /var/www/html/

# NOVO: a tela inicial do sistema e o client_login.php (e nao index.php),
# entao o Apache precisa saber qual arquivo abrir quando alguem acessa a
# raiz do site. Se um dia a tela inicial mudar de nome, troque aqui.
RUN echo "DirectoryIndex client_login.php index.php index.html" \
    > /etc/apache2/conf-enabled/zz-pistonpit-index.conf

# Configuracoes de PHP do ambiente online
# OBS: display_errors fica On agora para facilitar achar erro no primeiro
#      deploy. Depois que estiver tudo funcionando, troque para Off.
RUN { \
      echo "display_errors=On"; \
      echo "log_errors=On"; \
      echo "error_log=/dev/stderr"; \
      echo "upload_max_filesize=8M"; \
      echo "post_max_size=8M"; \
      echo "date.timezone=America/Sao_Paulo"; \
    } > /usr/local/etc/php/conf.d/pistonpit.ini

RUN chown -R www-data:www-data /var/www/html

# O Render define a porta na variavel $PORT e o Apache precisa escutar nela
# (por padrao o Apache escuta na 80, o que faz o deploy falhar no Render).
CMD ["sh", "-c", "sed -i \"s/^Listen 80$/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
