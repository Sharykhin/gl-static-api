# Give me a lift project

### Technology stack are:
 
This application is based on micro-service architecture.

Auth Service:
------------
#### frontend:
   - [angular](https://angular.io/docs) 4.4.6
    
#### backend:
   - [PHP 7.1](http://php.net/manual/en/)
   - [laravel  5.5.0](https://laravel.com/docs/5.5)
   - [MariaDB](https://mariadb.com/kb/en/)
   - [nginx](https://nginx.org/ru/docs/)

Static Service:
--------------

#### backend:
   - [PHP 7.1](http://php.net/manual/en/)
   - [Symfony 3.3](https://symfony.com/doc/3.3/setup.html)
   
Mail Service:
-------------

#### backend:
   - [Golang](https://golang.org/)
   
   
-----------

#### environment:
   - [docker containers](https://docs.docker.com/)
   
### Documentation

##### [Mail Service](./docs/mail/mails.md)
##### [Mail Service: API](./docs/mail/api.md)
##### [Static Service: API](./docs/static/readme.md)
##### [Auth Service :API](./docs/api/api.md)

### Get Started

Run all containers by using one docker command. It will run all the containers and
install test data. Currently only *dev* environment is supported well.

```bash
docker-compose up
```

##### Running mail service:
Mail service is based on [golang](https://golang.org/) 
and supports two environments: *dev* and *production*
By default this service will be run in *dev* mode and hot-module 
replacement will come to play:

```bash
docker-compose up mail-golang
docker-compose up mail-mysql
```

After running `mail-mysql` container first time you need to install test data:

```bash
docker-compose exec mail-mysql sh /tmp/post-run.sh
```

This command will create test *database* and *credentials* table in it.
Test credentials are: 
```json
{
	"api_key": "key1234",
	"secret_key":"secret1234"
}
```

To run it in production mode use `APP_ENV` variable: (**this may not work**)

```bash
APP_ENV=production docker-compose up mail-golang
```

This service links `mail-queue` service that provides queue for receiving messages.
The `mail-queue` service is described below.

Mail service receives the following JSON string format:
```json
{
  "action": "register",
  "payload": {}
}
```

The format is pretty simple and self-documented. 
`action` property provides information what kind of mail should be sent, 
for example: *register*, *startRoute*, *resetPassword*.  
Keep in mind that **camelCase** is in use

Example:
```json
{
  "action": "register",
  "payload": {
    "to" : "john@test.com",
    "name": "John",
    "activationLinkHash": "SomeHash"
  }
}
```

Currently [Mailgun](https://app.mailgun.com/app/dashboard) is in use 
for sending mails.
Use the following credentials: *siarhei.sharykhin@gmail.com/Tt2439868*  

All the failed messages(some issue on third-party service or anything else) 
will be logged in into a file and a cron job will try to send them again.
Cron job is run every 5 minutes.

If you have any questions don't hesitate to contact the author:  
Siarhei Sharykhin <siarhei.sharykhin@gmail.com>

#### Running mail queue service:
General mail service is used a queue it's [rabbitmq](https://www.rabbitmq.com)
for receiving messages use the following command to run the queue:

```bash
docker-compose up mail-queue
```

After that you manage your queue through web interface: [localhost:15672](http://localhost:15672/)
use *guest*/*guest* credentials.  
By default service exposes two ports: *15672* (for ui management) and *5672*.  
Feel free to contact an author: Siarhei Sharykhin <siarhei.sharykhin@gmail.com>  

#### Testing queue messages
Open [localhost:15672](http://localhost:15672/) with *guest/guest* credentials.  
Then go to [localhost:15672/#/queues/%2F/mail](http://localhost:15672/#/queues/%2F/mail)  
At the bottom there is Publish message option, open it:
Use the following message:

```json
{"action":"register", "payload":{"to":"siarhei.sharykhin@itechart-group.com", "name":"John"}}
```

or 
```json
{"action":"register", "payload":{"to":"artsem.vasilevich@itechart-group.com", "name":"John"}}
```

We use authorized recipients on mailgun, that's why we may have limited number of recipients.

#### Testing failed message:
To test failed message run a container with `APP_ENV=TEST_FAIL`
```bash
APP_MAIL=TEST_FAIL docker-compose up mail-golang
```

Then send a couple of messages by using rabbitmq management UI(see above)

Then open container terminal:
```bash
docker exec -i -t mail_service_golang /bin/bash
```
Go to restore-cron command file:
```bash
cd /go/src/mail/cmd/restore-cron
```
Run it with different `APP_ENV` value:
```bash
APP_ENV=dev go run main.go
```

#### Static File Upload
This service is responsible for uploading different images
and static files


Run docker services:
```bash
docker-compose up static-php
docker-compose up static-nginx
```

Install vendors:
```bash
docker-compose exec static-php bash
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
docker-compose exec static-php bash
cd /app/app/var
openssl genrsa -out private.pem 1024
openssl rsa -in private.pem -out public.pem -pubout
```

[Read documentation](docs/static/readme.md)