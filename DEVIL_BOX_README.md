# Devilbox (you may use something different for local development):

## Adding entry to /etc/hosts

```
127.0.0.1 geocoding.local
```

##Change devilbox .env


```
cd ~/Sites/devilboxes/php8.2
```

## change HOST_PATH_HTTPD_DATADIR in .env

```
HOST_PATH_HTTPD_DATADIR=/Users/cegrif01/Sites/php8.2
```

## change location of index.php file where package is booted in .env

```
HTTPD_DOCROOT_DIR=public
```

# the url of the devilbox package
```
http://geocoding.local:8082/
```

## Change port in .env
```
HOST_PORT_HTTPD=8082
```

#run devilbox from /Sites/devilboxes/php8.2

```
docker-compose up -d httpd mysql php
```