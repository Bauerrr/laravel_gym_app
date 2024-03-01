FROM php:8.2-cli as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-install pdo pdo_mysql bcmath

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

WORKDIR /var/www
COPY . .

COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

ENV PORT=8000
ENTRYPOINT ["docker/entrypoint.sh"]

# =======================================
# node
FROM node:21-alpine as node

WORKDIR /var/www
COPY ./gym_app_angular ./gym_app_angular
WORKDIR /var/www/gym_app_angular

RUN rm -rf node_modules
RUN rm -f package-lock.json

RUN npm install -g @angular/cli
RUN npm install

#ENV PORT=3000
CMD ["npm", "install"]
CMD ["ng", "serve", "--host", "0.0.0.0", "--port", "3000"]




