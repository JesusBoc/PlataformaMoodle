FROM moodlehq/moodle-php-apache:8.2

# 1. Instalar dependencias
RUN apt-get update && apt-get install -y \
    unzip git curl \
    && rm -rf /var/lib/apt/lists/*

# 2. Copiar el core de Moodle dentro de la imagen
COPY ./html /var/www/html

# 3. Instalar Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    chmod +x /usr/local/bin/composer && \
    rm composer-setup.php

# 4. Instalar dependencias PHP de Moodle
RUN cd /var/www/html && \
    composer install --no-dev --classmap-authoritative

# 5. Configuraciones PHP para Moodle
RUN echo "zend.exception_ignore_args = 1" >> /usr/local/etc/php/conf.d/moodle.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/moodle.ini
