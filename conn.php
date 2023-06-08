<?php

require_once 'config.php';

try {
    $dbh = new PDO(
        'mysql:host=localhost; db_name=u1265883_intensa_test;', $db_user, $db_pass, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);


} catch (PDOException $e) {
    // if connection fails, show PDO error.
    echo "Error connecting to mysql: " . $e->getMessage();
}