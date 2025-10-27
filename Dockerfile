# for arm64/v8 architecture
# FROM --platform=linux/arm64/v8 serversideup/php:8.4-fpm-nginx
# for amd64 architecture, use the line below instead
FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

COPY ./app /var/www/html

EXPOSE 8080
