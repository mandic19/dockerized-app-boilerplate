Dockerized web app boilerplate
============================

### Description

This is a sample project with a purpose to be a general template for a typical dockerized web application. 

###Technologies used:    

    ├── FRONTEND                      # React.JS (but any other JS Framework/Library will be suitable)
    ├── RESTFUL API                   # Yii2 PHP Framework
    ├── WEBSERVER                     # Nginx

### Directory structure

    ├── api                         # Yii2 Rest API template application
    ├── logs                        # NGINX access/error logs
    ├── nginx                       # Yii2 Rest API template application
    │   ├── dev              
    │   │   ├── default.config      # nginx default configuration for dev/local environment
    │   ├── prod              
    │   │   ├── default.config      # nginx default configuration for production environment
    ├── web                         # React web application template
    ├── docker-compose.yml          # docker-compose.yml boilerplate for dev/local environment
    ├── docker-compose-prod.yml     # docker-compose.yml boilerplate for production environment
    ├── docker-database.env         # database env file for dev/local environment
    ├── docker-database-prod.env    # database env file production environment
    └── README.md

### Development

1. In a project root run command: `docker-compose up --build`
2. In a project root run command: `winpty docker-compose exec api bash` and in the docker container:
    - Navigate to `application/api` subdirectory
    - Run `apt-get update`
    - Run `composer install`
    - Run `php init` and chose in which environment you want to run it
    - Run `php yii migrate`

### Production

1. In a project root run command: `docker-compose -f docker-compose-prod.yml up --build`
2. In a project root run command: `winpty docker-compose exec api bash` and in the docker container:
   - Navigate to `application/api` subdirectory
   - Run `apt-get update`
   - Run `composer install`
   - Run `php init` and chose in which environment you want to run it
   - Run `php yii migrate`
