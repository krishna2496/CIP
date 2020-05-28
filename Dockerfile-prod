FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /optimy
RUN rm /optimy/Dockerfile
# to do remove if not necessary
# RUN rm /optimy/Dockerfile.cli

# Install nodeJS
RUN apt-get install curl -y
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install -y nodejs

# Install api-admin dependencies and change privileges of storage
RUN cd /optimy/admin-api && composer install
RUN cd /optimy/admin-api && npm install
RUN chown -R www-data:www-data /optimy/admin-api/storage

# Install api dependencies and change privileges of storage
RUN cd /optimy/api && composer install
RUN cd /optimy/api && npm install
RUN chown -R www-data:www-data /optimy/api/storage

# Build access to frontend, api-doc and admin-api-doc
RUN mkdir /optimy/public

# Frontend
RUN mkdir /optimy/public/frontend
COPY ./frontend /tmp
RUN cd /tmp && mv vue-prod.config.js vue.config.js && npm install && npm rebuild node-sass && npm run build

RUN cp -R /tmp/dist/* /optimy/public/frontend/
RUN cp /tmp/htaccess /optimy/public/frontend/.htaccess

RUN rm -rf /tmp/*
RUN chown -R www-data:www-data /optimy/public/frontend

# Api-doc
RUN mkdir /optimy/public/api-doc
COPY ./api-doc /optimy/public/api-doc/
RUN chown -R www-data:www-data /optimy/public/api-doc
ENV SWAGGER_API_JSON="/optimy/public/api-doc/cip-api.json"

# Admin-api-doc
RUN mkdir /optimy/public/admin-api-doc
COPY ./admin-api-doc /optimy/public/admin-api-doc/
RUN chown -R www-data:www-data /optimy/public/admin-api-doc
ENV SWAGGER_ADMIN_API_JSON="/optimy/public/admin-api-doc/cip-admin-api.json"




