FROM composer
WORKDIR /data/
RUN composer require facebook/graph-sdk
COPY src/ .
ENTRYPOINT [ "php" ] 
CMD [ "fb-photo-backup.php" ]