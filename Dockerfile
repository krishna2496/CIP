FROM registry.kubernetes.infra.optimy.net/tools/web-ci:latest

COPY . /optimy
RUN rm /optimy/Dockerfile
RUN cd /optimy && composer install

#ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]

