# Task Manager in PHP
Semplice Task Manager sviluppato in PHP con PDO e MySQL, usando Bootstrap 5 per la parte grafica.

## Requisiti
- PHP 8.0+
- MySQL

## Come avviare il progetto
Clona o scarica questo repository. Crea il database MySQL con questa struttura minima:

```
CREATE DATABASE taskdb;
USE taskdb;

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open'
);
```

Crea un file config.php nella root del progetto (non è incluso nel repository). Deve contenere le credenziali del DB.
```
<?php
return [
    'host' => 'localhost',
    'dbname' => 'taskdb',
    'user' => 'root',
    'password' => 'tua_password_db',
];
```
