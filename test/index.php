<?php
require('../lib/http.php');
$app = new HTTP_PHP();

$app->get('/', function () {
    echo "<br>/ = homnhgge";
});
// $app->get('/a/:at/k', function () {
//     echo "<br>/ = at";
// });
// $app->get('/aka/:goals:fhg', function () {
//     echo "<br>/ = home";
// });

$app->get('/test/newexam/:id', function (Request $req) {
});
$app->get('/test/newexam', function (Request $req) {
});
$app->get('/test/:name/:age', function (Request $req) {
    var_dump($req->body());
    echo "<br>";
    var_dump($req->query());
    echo "<br>";
    var_dump($req->params());
});
$app->get('/test', function (Request $req, Response $res) {
    $res->status("206");
    $res->json(["Aa" => "g"]);
});

$app->get('/response-view', function (Request $req, Response $res) {
    $templates = ["views/header.html", "views/page1.html", "views/footer.html"];
    $data = [
        "page_title" => "response-view",
        "content" => "welcome to Http PHP by apu-hub <br> with Response middleware view",
    ];
    $res->view($templates,$data);
});

$app->get("/t", function (Request $req, Response $res) {
    $res->send("get");
});
$app->post("/t", function (Request $req, Response $res) {
    $res->send("post");
});
$app->put("/t", function (Request $req, Response $res) {
    $res->send("put");
});
$app->delete("/t", function (Request $req, Response $res) {
    $res->send("delete");
});


require_once("./router.php");
