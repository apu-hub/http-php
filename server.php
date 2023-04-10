<?php
session_start();

use App\HttpPhp;
use App\Middleware;

require_once(__DIR__ . "/vendor/autoload.php");
require_once(__DIR__ . "/routes/web.php");

// set time zone 
date_default_timezone_set("Asia/Kolkata");

// load environment variables 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new HttpPhp();

Middleware::cors();

$app->router('/', $web_router);
