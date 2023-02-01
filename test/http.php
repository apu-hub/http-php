<?php

require(__DIR__ . "/../lib/Http.php");


$requestArray = [
    ["test" => ["url" => "/sample-app", "uri" => "/sample-app", "method" => "GET"]],
    ["test" => ["url" => "/a/bg", "uri" => "/a/bg", "method" => "GET"]],
    ["test" => ["url" => "/a/b/c", "uri" => "/a/b/c", "method" => "GET"]],
    ["test" => ["url" => "/a/c", "uri" => "/a/c", "method" => "GET"]],
    ["test" => ["url" => "/a", "uri" => "/a", "method" => "GET"]],
];

$start_time = microtime(true);
foreach ($requestArray as $request) {
    testHttpApp($request);
}
$end_time = microtime(true);
$execution_time = ($end_time - $start_time);
echo "\nExecution in " . $execution_time . " sec";


function testHttpApp($request)
{
    require(__DIR__ . "/sample-app/sample_app.controller.php");
    // ############################# \\
    $app = new HttpPhp($request);
    $app->get("/sample-app", $GetSampleApp);

    $app->get('/', function () {
        echo "app \"/\"";
        echo "\n    |-> \"/\"";
    });

    // ############################# \\
    $g1 = $app->group('/a');
    $g1->get("/b", function () {
        echo "group \"/a\"";
        echo "\n      |-> \"/a/b\"";
    });

    // ############################# \\
    $g1c = $g1->group('/c');
    $g1c->get("/", function () {
        echo "group \"/a/c\"";
        echo "\n      |-> \"/a/c/\"";
    });

    // ############################# \\
    $g2 = $app->group('/a/b');

    $g2->get("/b", function () {
        echo "group \"/a/b\"";
        echo "\n      |-> \"/a/b/b\"";
    });

    $g2->get("/c", function () {
        echo "group \"/a/b\"";
        echo "\n      |-> \"/a/b/c\"";
    });

    // ############################# \\
    $app->get('/a', function () {
        echo "app \"/\"";
        echo "\n    |-> \"/a\"";
    });
}