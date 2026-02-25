FROM php:8.3-fpm-alpine

# Instalar dependências do sistema (Alpine Linux)
RUN apk add --no-cache \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    postgresql-dev \
    mysql-client \
    git \
    curl \
    zip \
    unzip \
    supervisor \
    oniguruma-dev

# Configurar GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# Instalar extensões PHP
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    gd \
    bcmath \
    mbstring \
    xml \
    fileinfo
# Instalar Composer globalmente
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

# Diretório de trabalho
WORKDIR /app

# Copiar arquivos do projeto
COPY . /app

# Criar diretórios necessários
RUN mkdir -p \
    storage/logs \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    bootstrap/cache

# Permissões
RUN chown -R www-data:www-data /app && \
    chmod -R 755 storage bootstrap/cache

EXPOSE 9000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
