<?php

require_once("request.php");
require_once("response.php");

class HTTP_PHP
{
    private $params_data = [];
    private $request_uri = null;

    function __construct(string $uri = null)
    {
        $this->request_uri = $uri;
    }

    function router(string $pattern, $handler)
    {
        if (empty(trim($pattern))) return;

        // if router pattern not matched
        if (!$this->pattern_checker($pattern, true)) return;

        // new uri
        $this->request_uri = ltrim($this->request_uri, $pattern);
        $this->request_uri = "/" . $this->request_uri;

        // execute the handler function
        $handler($this->request_uri);
        exit();
    }

    function get(string $pattern, $handler)
    {
        if (empty(trim($pattern))) return;
        // if METHOD and pattern not matched
        if ($_SERVER["REQUEST_METHOD"] !== "GET") return;

        // common handler
        $this->method_handler($pattern, $handler);
    }

    function post(string $pattern, $handler)
    {
        if (empty(trim($pattern))) return;
        // if METHOD and pattern not matched
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

        // common handler
        $this->method_handler($pattern, $handler);
    }

    function put(string $pattern, $handler)
    {
        if (empty(trim($pattern))) return;
        // if METHOD and pattern not matched
        if ($_SERVER["REQUEST_METHOD"] !== "PUT") return;

        // common handler
        $this->method_handler($pattern, $handler);
    }

    function delete(string $pattern, $handler)
    {
        if (empty(trim($pattern))) return;
        // if METHOD and pattern not matched
        if ($_SERVER["REQUEST_METHOD"] !== "DELETE") return;

        // common handler
        $this->method_handler($pattern, $handler);
    }

    function route(string $method, string $pattern, $handler)
    {
        if (empty(trim($method)) || empty(trim($pattern))) return;
        // if METHOD and pattern not matched
        if ($_SERVER["REQUEST_METHOD"] !== strtoupper($method)) return;

        // common handler
        $this->method_handler($pattern, $handler);
    }

    function method_handler($pattern, $handler)
    {
        // patter checking
        if (!$this->pattern_checker($pattern)) return;

        // if matched create Request Response
        $Request = new Request($this->params_data);
        $Response = new Response();

        // execute the handler function
        $handler($Request, $Response);
        $this->request_uri = null;
        exit();
    }

    function pattern_checker(string $pattern, bool $isRouter = false)
    {
        // get request uri
        if ($this->request_uri === null) {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        }

        // remove uri querys
        $uri = explode("?", $this->request_uri);

        // trim extra slash
        $uri = rtrim($uri[0], "/");
        $pattern = rtrim($pattern, "/");

        // separate all params
        $pattern_array = explode("/", $pattern);
        $uri_array = explode("/", $uri);


        // incase of router patter
        if ($isRouter) {
            if (count($pattern_array) <= count($uri_array)) {
                foreach ($pattern_array as $key => $value) {
                    if ($uri_array[$key] != $value) return false;
                }
                return true;
            }
            return false;
        } else {
            // regular patter
            // is params length not matched then return false
            if (count($pattern_array) !== count($uri_array)) return false;

            // check all request uri params one by one
            foreach ($uri_array as $key => $value) {

                // if pattern param not variable
                if (!preg_match_all("/:/i", $pattern_array[$key])) {

                    // if param not match then return false
                    if ($pattern_array[$key] != $value) return false;
                } else {

                    // if pattern param are variable
                    // ? clean the params
                    $param_key = ltrim($pattern_array[$key], ":");
                    $this->params_data[$param_key] = $value;
                }
            }
            return true;
        }
    }
}
