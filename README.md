
## About Assignment

The assignment was pretty straight forward. It was easy to implement and work on. Following are the key aspects you should consider while evaluating the assignment
- No frontend UI created.
- Functionality can be accessed as API endpoints.
- All core functions defined in assignment have been completed.
- No auth has been implemented (as advised in assignment). However can be easily implemented using Sanctum (Details further down).
- Only `list` and `store` functions defined for bookings (any other functions were not mentioned in the assignment).
- Events and Attendees have all functions implemented.
- Developed as dockerized containers.
- API Documentation published using Swagger.
- Pagination and filtering implemented for listing endpoints.
- Tests included for almost all the functions / features.
- Postman Collection included
- Data validation, serialization, normalization, concern separation taken care of.
- Laravel Pint used as a tool for maintaining code standards by fixing styles.

## Environment
- Docker Desktop with WSL2 integration
- Docker Compose
- PHP 8.2
- Laravel 12
- Mysql 8
- Nginx

## Libraries
- `laravel/sanctum` for managing auth. (auth not implemented).
- `darkaonline/l5-swagger` for generating Swagger documentation.
- `laravel/pint` for managing coding styles.

## How to use
- Clone main branch from GitHub (preferably inside WSL2) `git clone https://github.com/DanishMMir/event-booking-system-assignment.git`
- `cd` into root of the project `cd  event-booking-system-assignment/`
- pull and start docker containers using `docker-compose up -d`
- install dependencies by running `docker-compose run  --rm composer install`
- copy example env to env `docker-compose run  --rm server cp .env.example .env`
- generate app key `docker-compose run  --rm php artisan key:generate`
- connect your favourite MySQL tool (MySQL workbench) to the `mysql` docker container using below-mentioned credentials.
- create a database named `laravel_project` inside the `mysql` conatiner by connecting to it by following the above step.
- Add your DB creds to .env
```
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_project
    DB_USERNAME=homestead
    DB_PASSWORD=secret
```
- run migrations `php artisan migrate`
- The app should be live on `localhost:8080` now.
- Import the postman collection and environment provided in your postman.
- You should now be able to hit the endpoints using postman.
- Check Swagger documentation for the app on route http://localhost:8080/api/documentation

## What has been done
- Created database schemas necessary for the functionality.
- Implemented domain logic as per assignment to make the system work.
- Added proper API endpoints to be used for interacting with the system.
- Added test cases to cover functionality ad improve reliability.
- Added Postman Collection for easier access.

## Main Functionality
- Bring up the docker containers `docker-compose up -d`
- Head over to browser and hit http://localhost:8080/api/documentation
- Here you will find the documentation for all the endpoints of the application.
- Import the postman collection and environment in you postman application.
- This will enable you to test the endpoints of the application and tinker with different functionalities.
- You can also execute test suite by running `docker-compose run  --rm artisan test` command inside the project root.

## Improvements that can be made
- Auth can be implemented
- many more

## What has not been done / was unclear
- Auth Implementation has not been done as was mentioned in the assignment.
- Schemas have been kept simple on purpose due to time constriant.
- Not all endpoints have been implemented for bookings. Only `list` and `store` endpoints implemented.
