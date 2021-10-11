<?php

return [
    'dsn'  => 'mysql:dbname=;host=localhost',
    'user' => 'root',
    'pass' => 'root',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
];
