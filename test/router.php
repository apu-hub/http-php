<?php
require_once('../lib/http.php');

$router = new HTTP_PHP();

$router->router("/rt", function ($uri) {
    $app = new HTTP_PHP($uri);
    $app->get("/", function (Request $req, Response $res) {
        $res->send("welcome ROUTER");
    });
    $app->get("/home", function (Request $req, Response $res) {
        $res->send("ROUTER home");
    });
    $app->router("/admin", function ($uri) {
        $admin = new HTTP_PHP($uri);
        $admin->get("/", function (Request $req, Response $res) {
            $res->send("welcome admin");
        });
        $admin->get("/home/:name/okay/:age", function (Request $req, Response $res) {
            // $res->send("admin home");
            $res->json($req->params());
        });
    });
    $app->router("/public", function ($uri) {
        $public = new HTTP_PHP($uri);
        $public->get("/", function (Request $req, Response $res) {
            $res->send("welcome public");
        });
        $public->get("/home", function (Request $req, Response $res) {
            $res->send("public home");
        });
    });
});
