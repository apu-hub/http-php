[![HTTP PHP Logo](https://i.ibb.co/9gZ7KhX/logo-03.png)](http://github.com/apu-hub/http-php/)

It's a simple HTTP request handling library, written in PHP.
build and tested on PHP 7.4.12 .

## Quick Start

download example with libary from [here](https://minhaskamal.github.io/DownGit/#/home?url=https://github.com/apu-hub/http-php/tree/main/examples).

or

download the latest version of the [library](https://minhaskamal.github.io/DownGit/#/home?url=https://github.com/apu-hub/http-php/tree/main/dist).

```php
require_once 'PATH_OF_LIBRARY/http.php';

$app = new Http();

$app->get('/', function (Request $request, Response $response) {
    $response->send('Hello World!');
});
```

## documentation:

- [Routing](#routing)
  - [Routes](#routes)
  - [Router](#router)
- [Request](#request)
  - [Body](#body)
  - [Query](#query)
  - [Params](#params)
- [Response](#response)
  - [Status](#status)
  - [Send](#send)
  - [json](#json)
  - [view](#view)

# Routing

## Routes

Depending on the request method currentlly available class functions are:

get, post, put, delete, route.

```php
// $app->get('/path', function());
// $app->post('/path', function());
// $app->put('/path', function());
// $app->delete('/path', function());
$app->get('/', function (Request $request, Response $response) {
    $response->send('Hello World!');
});
```

```php
// $app->route('method', '/path', function());
$app->route('GET','/', function (Request $request, Response $response) {
    $response->send('Hello World!');
});
```

## Router

This function is usefull to create nested routes.

```php
// $app->router('/path', function() {
$app->get('/items', function ($uri) {

    $items = new HTTP_PHP();

    $items->get('/', function ($uri) {
        $items->send('Sending all items');
    });
    $items->get('/:id', function ($uri) {
        $items->send('send item');
    });
    $items->post('/:id', function ($uri) {
        $items->send('create item');
    });
    $items->put('/:id', function ($uri) {
        $items->send('update item');
    });
    $items->delete('/:id', function ($uri) {
        $items->send('delete item');
    });
});
```

# Request

## Body

```php
// body(); returns the body of the request as a array
// body('key'); returns the value of the key
$app->get('/', function (Request $request, Response $response) {
    $body = $request->body();
    $response->json($body);
});
```

## Query

```php
// request query string : ?key=value
// result : ["key" => "value"]

// query(); returns the query of the request as a array
// query('key'); returns the value of the key
$app->get('/', function (Request $request, Response $response) {
    $query = $request->query();
    $response->json($query);
});
```

## Params

```php
// request params : /1234
// result : ["key"=>"1234"]

// params(); returns the params of the request as a array
// params('key'); returns the value of the key
$app->get('/:key', function (Request $request, Response $response) {
    $params = $request->params();
    $response->json($params);
});
```

# Response

## Send

```php
$app->get('/', function (Request $request, Response $response) {
    $response->send('Hello World!');
});
```

## Json

```php
// jsson(); array as input
// json(); returns the body to client as json
$app->get('/', function (Request $request, Response $response) {
    $body = ['key' => 'value'];
    $response->json($body);
});
```

## View

This function is usefull to render a view. [sample file](https://github.com/apu-hub/http-php/tree/main/examples/views) for views. file path reference use `views/footer.html` instead of `views\footer.html`. [list of special php characters](https://stackoverflow.com/questions/16431280/list-of-special-php-characters#answer-16431310)

```php
$app->get('/page1', function (Request $request, Response $response) {

    $templates=["views/header.html",
                "views/page1.html",
                "views/footer.html"];

    $data = ["page_title" => "Page 1",
             "page_content" => "Content of page 1"];

    // view([templates_path_array], [data_array])
    $response->view($templates, $data);
});
```

# Contributing

This project is open-source, you can contribute to it by making a pull request or opening an issue.

## People

Author of HTTP PHP is [Chayan Sarkar](https://github.com/apu-hub/)

