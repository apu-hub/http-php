<?php
class Response
{
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
    }

    function json(array $response)
    {
        if (isset($response) && (!empty($response)))
            echo json_encode($response);
        else
            echo json_encode([]);
        exit();
    }

    function view(array $templates, array $data = null)
    {
        $content = null;

        foreach ($templates as $key => $file_path) {
            // get absulute path
            $file_path = join(DIRECTORY_SEPARATOR, array($_SERVER['DOCUMENT_ROOT'], $file_path));

            // check is file exists
            if (file_exists($file_path)) {
                // fetch content from file
                // ! error handler
                $content .= file_get_contents($file_path);
            }
        }

        if (gettype($data) != "array") $this->send($content);

        // parse parameter
        foreach ($data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }
        $this->send($content);
    }
}
