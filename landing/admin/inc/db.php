<?php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die;

/**
 *  Database connection
 */
 
$DB = new mysqli('localhost', 'root', '', '12player');
//$DB = new mysqli('12player.mysql.database.azure.com', '<username>', '<password>', '12playerdb');

if ($DB->connect_errno) {
    printf('Error connectig to the database: %s' . PHP_EOL, $DB->connect_errno);
    die;
}

$DB->set_charset('utf8mb4');

?>