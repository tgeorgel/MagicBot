<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('VERSION', "2.0");

require_once 'vendor/autoload.php';
require_once 'inc/functions.php';


echo "\n\n•••|     MagicBot v" . VERSION . "     |•••\n\n";


$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');

$bot = \App\Classes\Bot::getinstance();


$bot->startup();
$bot->loop();
