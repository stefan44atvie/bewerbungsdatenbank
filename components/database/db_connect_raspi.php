<?php

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'pi');
define('DB_PASSWORD', 'pi');
define('DB_NAME', 'bewerbungsdatenbank');

// create connection
$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// check connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}