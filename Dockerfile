FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /optimy
RUN rm /optimy/Dockerfile
RUN mkdir -p /optimy/database/seeds

RUN cd /optimy && composer install

RUN chown -R www-data:www-data /optimy/storage

#ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]

