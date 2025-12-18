FROM php:8.2-cli-alpine

# Installation des dépendances pour PostgreSQL
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Commande par défaut pour lancer le serveur
CMD ["php", "-S", "0.0.0.0:80", "-t", "/app/back/public"]
