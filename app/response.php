<?php
namespace App;
use App\Session;

class Response
{
    protected string $redirect_url = "";
    protected Session $session;
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
        extract($data);
        foreach ($templates as $key => $file_path) {
            $app_root = dirname(dirname(__FILE__));
            // get absulute path
            $file_path = join(DIRECTORY_SEPARATOR, array($app_root, $file_path));
            // check is file exists
            if (!file_exists($file_path)) {
                $message = "View File " . basename($file_path) . " Not Found";
                throw new \Exception($message);
            }

            include($file_path);
        }
        return $this;
    }

    public function back()
    {
        $this->redirect_url = $_SERVER['HTTP_REFERER'] ?? "";
        $this->session = new Session("back");
        return $this;
    }

    public function redirect(string $url)
    {
        $this->redirect_url = $url;
        $this->session = new Session("redirect");
        return $this;
    }

    public function with(string $key, $value)
    {
        $this->session->with($key, $value);
        Utils::phpRedirect($this->redirect_url);
    }
}
