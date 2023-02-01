<?php
$GetSampleApp = function (Response $res) {
    $templates = [
        "test/sample-app/index.sample_app.view.php",
    ];

    $data = ["version" => 3,"map"=>["A","B","C"]];

    $res->view($templates,$data);
    exit;
};
