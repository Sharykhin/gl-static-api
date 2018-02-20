Static API Service:
===================

Requirements:
------------

- [docker](https://docs.docker.com/)

Dependencies:
------------

   - [PHP 7.1](http://php.net/manual/en/)
   - [Symfony 3.3](https://symfony.com/doc/3.3/setup.html)
   
  
Documentation:
-------------

##### [Static Service: API](./doc/api.md)

Usage:
------

This service is responsible for uploading different images
and static files

Build images:
```bash
docker-compose build
```

Run containers:
```bash
docker-compose up
```

Install vendors:
```bash
docker-compose exec gl-static-api-php bash
cd /app
composer install
```

Running test:
```bash
docker-compose exec static-php bash
cd /app
./vendor/bin/phpunit
```
As tests are run the code coverage statistic 
will be available under [localhost:8002/codeCoverage/index.html](http://localhost:8002/codeCoverage/index.html)

For testing JWT (**just for beta tests** since some time late we will use auth service)
you need to generate private public keys:
 
```bash
docker-compose exec gl-static-api-php bash
cd /app/app/var
openssl genrsa -out private.pem 1024
openssl rsa -in private.pem -out public.pem -pubout
```