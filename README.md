
## About Assignment

The assignment was pretty straight forward. It was easy to implement and work on. Following are the key aspects you should consider while evaluating the assignment
- No frontend UI created.
- Functionality can be accessed as API endpoints.
- All core functions defined in assignment have been completed.
- No auth has been implemented (as advised in assignment). However, can be easily implemented using Sanctum (Details further down).
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
- It will install dependencies, create app key and copy .env.example into .env
- connect your favourite MySQL tool (MySQL workbench) to the `mysql` docker container using below-mentioned credentials.
```
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_project
    DB_USERNAME=homestead
    DB_PASSWORD=secret
```
- create a database named `laravel_project` (If not already created during docker container setup) inside the `mysql` container by connecting to it by following the above step.
- run migrations `docker-compose run  --rm artisan migrate`
- The app should be live on `localhost:8080` now. It should show the Laravel welcome page.
- To open the documentation open http://localhost:8080/api/documentation
- Import the postman collection `Event Booking System Assignment.postman_collection.json` provided in this repo  in your postman.
- Import the postman environment `Event Booking System Environment.postman_environment.json` provided in this repo in your postman.
- You should now be able to hit the endpoints using postman.

## What has been done
- Created database schemas necessary for the functionality.
- Implemented domain logic as per assignment to make the system work.
- Added proper API endpoints to be used for interacting with the system.
- Added test cases to cover functionality and improve reliability.
- Added postman Collection for easier access.

## Main Functionality
- Bring up the docker containers `docker-compose up -d`
- Head over to browser and hit http://localhost:8080/api/documentation
- Here you will find the documentation for all the endpoints of the application.
- Import the postman collection and environment in you postman application.
- This will enable you to test the endpoints of the application and tinker with different functionalities.
- You can also execute test suite by running `docker-compose run  --rm artisan test` command inside the project root.

## How can Authentication and Authorization be implemented.
- This can be implemented by using Laravel Sanctum package in combination with Laravel Fortify package.
- Laravel Sanctum will handle the authentication for API as well as any session and cookie based frontend.
- Laravel Fortify will handle user authentication and authorization by providing implementations for registration, login, password_resets, 2FAs, etc.
- Laravel Gates and Policies can be used to implement different roles and permissions for users in the system.
- The routes that need to be behind authentication need to be wrapped inside the `auth` and `sanctum` middlewares.
- As can be seen in the `routes/api.php` file, the event routes apart from list and show are wrapped inside `auth` and `sanctum` middleware.
- If this is uncommented, then these event routes will only be accessible with a valid authorization token provided in request.
- All other routes will continue to work without authorization token.

## Improvements that can be made
- Auth can be implemented.
- More elaborate and complex database schema can be defined.
- A frontend UI can be created.
- The remaining bookings endpoints can be created.
- coding standards can be improved by adding tools like phpstan, psalm, etc.
- A local build pipeline can be created to check if the new code follows the defined coding standards.
- This build can be extended to GitHub Actions to run more thorough builds.
- Proper role based authorization can be implemented.
- many more.

## What has not been done
- Auth Implementation has not been done as was mentioned in the assignment.
- Schemas have been kept simple on purpose due to time constraint.
- Not all endpoints have been implemented for bookings. Only `list` and `store` endpoints implemented.
