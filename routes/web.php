<?php

use App\HttpPhp;
use App\Response;

$web_router = function (HttpPhp $app) {
    // import controllers
    require_once(__DIR__ . "/../Features/Home/controller.Home.php");

    // routes
    $app->get('/', $index_Home);
};
