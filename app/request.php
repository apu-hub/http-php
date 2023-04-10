<?php

namespace App;

class Request
{
    private $body_data = [];
    private $query_data = [];
    private $params_data = [];
    private $headers_data = [];

    function __construct(array $params_data)
    {
        // store all value in associative array format
        $this->query_data = $_REQUEST;
        $this->params_data = $params_data;
        $temp_body = json_decode(file_get_contents('php://input'), true);
        $this->body_data = $temp_body ? $temp_body : [];
        $this->headers_data = getallheaders();
    }

    function getallheaders()
    {

        $headers = [];

        foreach ($_SERVER as $name => $value) {

            if (substr($name, 0, 5) == 'HTTP_') {

                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    function clean_input($input)
    {
        return htmlspecialchars(stripslashes(trim($input)));
    }

    function body(string $key = "", string $default = "")
    {
        // if key not set and empty return all body data
        if (!isset($key) || empty($key)) {
            return $this->body_data;
        }

        if (isset($this->body_data[$key]) && !empty($this->body_data[$key])) {
            return $this->clean_input($this->body_data[$key]);
        }

        return $default;
    }

    function query(string $key = "", string $default = "")
    {
        // if key not set and empty return all query data
        if (!isset($key) || empty($key)) {
            return $this->query_data;
        }

        if (isset($this->query_data[$key]) && !empty($this->query_data[$key])) {
            return $this->clean_input($this->query_data[$key]);
        }

        return $default;
    }

    function params(string $key = "", string $default = "")
    {
        // if key not set and empty return all query data
        if (!isset($key) || empty($key)) {
            return $this->params_data;
        }

        if (isset($this->params_data[$key]) && !empty($this->params_data[$key])) {
            return $this->clean_input($this->params_data[$key]);
        }

        return $default;
    }

    function headers(string $key = "", string $default = "")
    {
        // if key not set and empty return all query data
        if (!isset($key) || empty($key)) {
            return $this->headers_data;
        }

        if (isset($this->headers_data[$key]) && !empty($this->headers_data[$key])) {
            return $this->clean_input($this->headers_data[$key]);
        }

        return $default;
    }
}
