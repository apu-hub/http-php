<?php
class Request
{
    private $body_data = [];
    private $query_data = [];
    private $params_data = [];

    function __construct(array $params_data)
    {
        // store all value in associative array format
        $this->query_data = $_REQUEST;
        $this->params_data = $params_data;
        $temp_body = json_decode(file_get_contents('php://input'), true);
        $this->body_data = $temp_body ? $temp_body : [];
    }

    function clean_input($input)
    {
        return htmlspecialchars(stripslashes(trim($input)));
    }

    function body(string $key = "")
    {
        // if key set and not empty return key value
        if (isset($key) && !empty($key)) {
            if (isset($this->body_data[$key]) && !empty($this->body_data[$key])) {
                return $this->clean_input($this->body_data[$key]);
            } else {
                return null;
            }
        } else {
            // return all body data
            return $this->body_data;
        }
    }

    function query(string $key = "")
    {
        // if key set and not empty return key value
        if (isset($key) && !empty($key)) {
            if (isset($this->query_data[$key]) && !empty($this->query_data[$key])) {
                return $this->clean_input($this->query_data[$key]);
            } else {
                return null;
            }
        } else {
            // return all query data
            return $this->query_data;
        }
    }

    function params(string $key = "")
    {
        // if key set and not empty return key value
        if (isset($key) && !empty($key)) {
            if (isset($this->params_data[$key]) && !empty($this->params_data[$key])) {
                return $this->clean_input($this->params_data[$key]);
            } else {
                return null;
            }
        } else {
            // return all query data
            return $this->params_data;
        }
    }
}
