FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

RUN mkdir -p /optimy/public
COPY . /optimy/public
RUN chown -R www-data:www-data /optimy/public
ENV SWAGGER_JSON="/optimy/public/cip-api.json"