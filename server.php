<?php
// session_start();

use App\HttpPhp;

require_once(__DIR__ . "/vendor/autoload.php");
require_once(__DIR__ . "/routes/web.php");

$app = new HttpPhp();

$app->get('/', function () {
    echo "HH___________HH";
});

$app->router('/', $web_router);
