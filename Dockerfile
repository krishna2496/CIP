FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /optimy
RUN rm /optimy/Dockerfile
RUN cd /optimy && composer install
RUN mkdir /optimy/database/seeds

RUN chown -R www-data:www-data /optimy/storage

#ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]

