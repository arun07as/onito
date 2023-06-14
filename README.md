# Table Of Contents
- [Table Of Contents](#table-of-contents)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Running Tests](#running-tests)

# System Requirements
This Application uses the following

- PHP 8.2
- MySQL 8
- Composer 2

# Installation
From your project root directory, run
```sh
.bin/setup
```
This will perform a basic setup.

Next fill in your db credentials in .env file 
```
DB_DATABASE=databaseName
DB_USERNAME=databaseUserName
DB_PASSWORD=databaseUserPassword
```
Next Run
```
php artisan migrate --seed
```

You can now run your application by running the following command from the project root directory
```sh
php artisan serve
```
and then opening your browser to http://127.0.0.1:8000/healthcheck


# Running Tests
You can run the following command to run tests
```sh
php artisan test
```
