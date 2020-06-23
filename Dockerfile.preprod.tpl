FROM registry.kubernetes.infra.optimy.net/ci/ci-source:{{GO_PIPELINE_LABEL}}



# Frontend
COPY ./frontend /tmp
RUN cd /tmp && mv vue-prod.config.js vue.config.js && npm install && npm rebuild node-sass && npm run build-preprod

RUN cp -R /tmp/dist/* /optimy/frontend/public
RUN cp /tmp/htaccess /optimy/frontend/public/.htaccess

RUN rm -rf /tmp/*

# Api-doc
ENV SWAGGER_API_JSON="/optimy/api-doc/cip-api.json"

# Admin-api-doc
ENV SWAGGER_ADMIN_API_JSON="/optimy/admin-api-doc/cip-admin-api.json"

RUN chown -R www-data:www-data /optimy/




