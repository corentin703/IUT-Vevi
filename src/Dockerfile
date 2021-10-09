FROM php:7-apache
	
RUN \
	docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

WORKDIR /var/www/html/

RUN \
	a2enmod rewrite
