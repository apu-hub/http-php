<?php

namespace App;

use App\Session;

class Response
{
    protected string $redirect_url = "";
    protected Session $session;

    function __construct()
    {
        $this->session = new Session("request");
        $this->redirect_url = $_SERVER['HTTP_REFERER'] ?? "";
    }
    function send(string $message)
    {
        echo $message;
        exit();
    }

    function status(int $response_code)
    {
        if (isset($response_code) && (!empty($response_code)))
            http_response_code($response_code);
        else
            http_response_code(200);

        return $this;
    }

    function json(array $response)
    {
        if (isset($response) && (!empty($response)))
            echo json_encode($response);
        else
            echo json_encode([]);
        exit();
    }

    function view(array $templates, array $data = [])
    {
        $app_root = dirname(__FILE__);
        $feature_page = join(DIRECTORY_SEPARATOR, [dirname($app_root), "features"]);

        $server_data = [];
        // generate csrf
        $csrf_token = bin2hex(random_bytes(32));
        $this->session->with('csrf_token', $csrf_token);
        $server_data['csrf'] = "<input type='hidden' name='csrf_token' value='$csrf_token'>";
        $server_data['csrf_token'] = $csrf_token;

        extract(array_merge($data, $server_data));
        foreach ($templates as $key => $view_path) {
            $temp = explode(".", $view_path);
            // .view.Gallery.php
            $file_name = $temp[1] . ".view." . $temp[0] . ".php";
            $file_path = join(DIRECTORY_SEPARATOR, [$feature_page, $temp[0], $file_name]);
            // get absulute path
            // check is file exists
            if (!file_exists($file_path)) {
                $message = "View File " . basename($file_path) . " Not Found";
                throw new \Exception($message);
            }

            include($file_path);
        }
        return $this;
    }

    public function return()
    {
        Utils::phpRedirect($this->redirect_url);
    }

    public function back()
    {
        return $this;
    }

    public function redirect(string $url)
    {
        $this->redirect_url = $url;
        return $this;
    }

    public function with($key, $value = "")
    {
        if (!is_array($key)) {
            $this->session->with($key, $value);
        } else {
            foreach ($key as $index => $data) {
                $this->session->with($index, $data);
            }
        }
        Utils::phpRedirect($this->redirect_url);
    }

    public function startSession(string $prefix)
    {
        return new Session($prefix);
    }
}
