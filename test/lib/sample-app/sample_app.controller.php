<?php

use App\Response;

$GetSampleApp = function (Response $res) {
    $templates = [
        "test/lib/sample-app/index.sample_app.view.php",
    ];

    $data = ["version" => 3,"map"=>["A","B","C"]];

    $res->view($templates,$data);
    exit;
};
