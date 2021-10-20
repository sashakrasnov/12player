<?php

include_once 'func.php';

session_start();

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Moscow');


define('SALT_KEY', '<salt_key>');                // Salt key
define('TITLE', '12th Player > Control panel');  // название сайта
//define('RUB', '&#8381;');                      // RUR symbol 1
define('RUB', '<i class="fa fa-rub"></i>');      // RUR symbol 2
define('UPLOAD_DIR', '/images');                 // Image folder
define('NUM_IMAGES', 2);                         // Number of images per event


$URI = explode('?', $_SERVER['REQUEST_URI'])[0];
$CFG = json_decode(@file_get_contents('../langs/cfg.en.json'), true);

$ADMIN_PAGE_LOC = 'Location: '.$URI;


// Let's check configuration
if(!$CFG)
    die('Control panel configuration error.');


// Creating config variables
foreach ($CFG as $k => $v) {
    for ($i=0; isset($v[$i]); $i++) {
        ${'C_'.$k}[$v[$i]['id']] = $v[$i];

        unset(${'C_'.$k}[$v[$i]['id']]['id']);
    }

    asort(${'C_'.$k});
}

?>