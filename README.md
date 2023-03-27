Trello Clone
============================

### Description

This is a sample project with a purpose to replicate Trello board and it's functionalities.

###Technologies used:    

    ├── FRONTEND                      # React.JS
    ├── RESTFUL API                   # Yii2 PHP Framework
    ├── WEBSERVER                     # Nginx

### Directory structure

    ├── api                                  # Yii2 Rest API template application
    ├── web                                  # React web application template
    ├── logs                                 # Nginx access/error logs
    ├── docker                               # Dockerization config
    │   ├── dev
    │   │   ├── web
    │   │   │   ├── Dockerfile               # FRONTEND container build instructions
    │   │   ├── nginx                        
    │   │   │   ├── default.config           # Nginx default configuration
    │   │   ├── api
    │   │   │   ├── Dockerfile               # API container build instructions
    │   │   │   ├── pho-ini-overrides.ini    # Config for overriding php.ini settings
    │   │   ├── .env                         # Environment variables
    │   │   ├── docker-compose.yml           # Docker compose boilerplate file
    │   │   ├── docker-database.env          # Database environment variables
    │   ├── prod
    │   │   ├── nginx                        
    │   │   │   ├── Dockerfile               # Nginx container build instructions
    │   │   │   ├── default.config           # Nginx default configuration
    │   │   ├── api
    │   │   │   ├── Dockerfile               # API container build instructions
    │   │   │   ├── pho-ini-overrides.ini    # Config for overriding php.ini settings
    │   │   ├── .env                         # Environment variables
    │   │   ├── docker-compose.yml           # Docker compose boilerplate file
    │   │   ├── docker-database.env          # Database environment variables
    └── README.md


## Development

1. Navigate to a following directory: `cd docker/dev`
2. Run command: `docker-compose -p "app_name" up --build`
3. Run command: `winpty docker-compose exec api bash` and in the docker container:
   - Navigate to `application/api` subdirectory
   - Run `apt-get update`
   - Run `composer install`
   - Run `php init` and chose in which environment you want to run it
   - Run `php yii migrate`

## Production

1. Navigate to a following directory: `cd docker/prod`
2. Run command: `docker-compose -p "app_name" up --build`
3. Run command: `winpty docker-compose exec api bash` and in the docker container:
   - Navigate to `application/api` subdirectory
   - Run `apt-get update`
   - Run `composer install`
   - Run `php init` and chose in which environment you want to run it
   - Run `php yii migrate`
