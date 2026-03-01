ARG NGINX_VERSION=1.29

FROM nginx:${NGINX_VERSION}-alpine

COPY ./.devops/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY ./public /srv/public
