<?php

use App\Request;
use App\Response;

// view controllers
$index_Home = function (Response $res, Request $req) {
    $templates = [
        "Home.show",
    ];

    $data = ["app_name" => "Http-Php"];

    $res->view($templates,$data);
    return 1;
};