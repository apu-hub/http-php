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
        $app_root = dirname(__FILE__);
        $feature_page = join(DIRECTORY_SEPARATOR, [dirname($app_root), "features"]);

        extract($data);
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
