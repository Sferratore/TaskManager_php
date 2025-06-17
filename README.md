# Task Manager in PHP
Simple Task Manager developed in PHP using PDO and MySQL, with Bootstrap 5 for the frontend.

## Requirements
- PHP 8.0+
- MySQL

## How to Run the Project
Clone or download this repository. Create the MySQL database with the following minimal structure:

```
CREATE DATABASE taskdb;
USE taskdb;

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open'
);
```

Create a `config.php` file in the root directory of the project (not included in the repository). It must contain the database credentials:

```
<?php
return [
    'host' => 'localhost',
    'dbname' => 'taskdb',
    'user' => 'root',
    'password' => 'your_db_password',
];
```
