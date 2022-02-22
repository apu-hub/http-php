<?php
require_once('http-php/http.php');
$app = new HTTP_PHP();

// basic routing
$app->get('/', function () {
    echo "Request method is GET";
});
$app->post('/', function () {
    echo "Request method is POST";
});
$app->put('/', function () {
    echo "Request method is PUT";
});
$app->delete('/', function () {
    echo "Request method is DELETE";
});

// with Request and Response class
$app->get('/response-send', function (Request $req, Response $res) {
    $res->send("GET : welcome to Http PHP by apu-hub <br> with Response send");
});

$app->get('/response-json', function (Request $req, Response $res) {
    $response = array(
        'method' => 'get',
        'url' => '/response-json',
    );
    $res->json($response);
});

$app->get('/response-view', function (Request $req, Response $res) {
    $templates = ["views/header.html", "views/page1.html", "views/footer.html"];
    $data = [
        "page_title" => "response-view",
        "content" => "welcome to Http PHP by apu-hub <br> with Response view",
    ];
    $res->view($templates, $data);
});

// example uri : localhost:8080/request-body
// with body 
$app->get('/request-body', function (Request $req, Response $res) {
    $body = $req->body();
    $response = array(
        'method' => 'get',
        'url' => '/request-body',
        'body' => $body
    );
    $res->json($response);
});

// example uri : localhost:8080/request-query?name=apu-hub&age=20
$app->get('/request-query', function (Request $req, Response $res) {
    $query = $req->query();
    $response = array(
        'method' => 'get',
        'url' => '/request-query',
        'query' => $query
    );
    $res->json($response);
});

// example uri : localhost:8080/request-params/apu-hub
$app->get('/request-params/:name', function (Request $req, Response $res) {
    $params = $req->params();
    $response = array(
        'method' => 'get',
        'url' => '/request-params/:name',
        'params' => $params
    );
    $res->json($response);
});
