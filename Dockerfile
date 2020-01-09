FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /optimy
RUN rm /optimy/Dockerfile
RUN cd /optimy && composer install

#ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]

