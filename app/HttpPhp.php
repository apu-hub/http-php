<?php
namespace App;
use App\Request;
use App\Response;
use App\Uri;

class HttpPhp
{
    public $params = [];
    private $request_uri = null;
    private  $previous_path = "";
    private $requestData = [
        "url" => "",
        "uri" => "",
        "method" => "",
        "body" => null,
        "query" => null,
    ];
    private $config = [
        "request_uri" => null,
        "previous_path" => null,
        "request_url" => null,
        "request_method" => null,
        "test" => null,
    ];

    // function __construct(string $request_uri = null, string $previous_path = null)
    function __construct(array $config = [
        "request_uri" => null,
        "previous_path" => null,
        "request_url" => null,
        "request_method" => null,
        "test" => null,
    ])
    {
        // set config value
        $this->setConfig($config);

        // ! ### test ### ! \\
        if ($this->getConfig("test") != null) {
            echo "\nApp Running Under Test Mode...\n\n";
            // $this->setConfig($this->getConfig("test"));
            $this->setRequestData($this->getConfig("test"));
            echo "================ test \n";
            // var_dump($this->config);
            // var_dump($this->requestData);
            echo "url => ", $this->getRequestData("url"), "\n";
            echo "uri => ", $this->getRequestData("uri"), "\n";
            return;
        }

        // construct call by group
        if (
            $this->getConfig("request_uri") != null &&
            $this->getConfig("previous_path") != null &&
            $this->getConfig("request_method") != null &&
            $this->getConfig("request_url") != null
        ) {
            $this->setRequestData([
                "url" => $this->getConfig("request_url"),
                "method" => $this->getConfig("request_method"),
                "uri" => $this->getConfig("request_uri"),
            ]);
            echo "================ neasted \n";
            // var_dump($this->config);
            // var_dump($this->requestData);
            echo "url => ", $this->getRequestData("url"), "\n";
            echo "uri => ", $this->getRequestData("uri"), "\n";
            return;
        }

        $this->setRequestData();
        echo "================ defualt \n";
        // var_dump($this->config);
        // var_dump($this->requestData);
        echo "url => ", $this->getRequestData("url"), "\n";
        echo "uri => ", $this->getRequestData("uri"), "\n";

        // // ! Request URI
        // if ($this->getConfig('request_uri') != null)
        //     $this->request_uri = $this->getConfig('request_uri');
        // else
        //     $this->request_uri = $_SERVER['REQUEST_URI'];

        // // Previous Path
        // if ($this->getConfig('previous_path') != null)
        //     $this->previous_path = $this->getConfig('previous_path') ?? "";
    }

    /**
     * Set Request Data
     */
    protected function setRequestData(array $requestData = null)
    {
        $keys = array_keys($this->requestData);

        // ! incase test
        if ($requestData != null) {
            for ($i = 0; $i < count($keys); $i++) {
                if (isset($requestData[$keys[$i]])) {
                    $this->requestData[$keys[$i]] = $requestData[$keys[$i]];
                }
            }
            return;
        }

        // * add others
        $this->requestData = [
            "url" => $_SERVER['REQUEST_URI'],
            "uri" => $_SERVER['REQUEST_URI'],
            "method" => $_SERVER["REQUEST_METHOD"],
            "body" => json_decode(file_get_contents('php://input'), true),
            "query" => $_REQUEST,
        ];
    }

    /**
     * Get Config Data
     */
    protected function getRequestData(string $key)
    {
        return isset($this->requestData[$key]) ? $this->requestData[$key] : null;
    }

    /**
     * Set Config Data
     */
    protected function setConfig(array $config)
    {
        $keys = array_keys($this->config);
        for ($i = 0; $i < count($keys); $i++) {
            if (isset($config[$keys[$i]])) {
                $this->config[$keys[$i]] = $config[$keys[$i]];
            }
        }
    }

    /**
     * Get Config Data
     */
    protected function getConfig(string $key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    /**
     * Get Method
     */
    public function get(string $path, $handler)
    {
        echo "\nGET =======>\n";
        // if METHOD and pattern not matched
        if ($this->getRequestData("method") !== "GET") return $this;
        // add previous path
        $currentPath = Uri::cleaner($path);
        // path not matched
        echo "uri => ", $this->getRequestData("uri"), "\n";
        echo "path => ", $path, "\n";
        if (!Uri::matchUriAndPath($this->getRequestData("uri"), $path)) return $this;
        echo "match\n";
        // parse param data
        $fullPath = Uri::cleaner($this->getConfig("previous_path") ?? "/") . "/" . Uri::cleaner($path);
        $this->params = Uri::parseParamData($this->getRequestData("url"), $fullPath);

        // check handler
        if (!is_callable($handler))
            throw new \Exception("handler is not callable");

        $request = new Request($this->params);
        $response = new Response();

        // execute the handler function
        $handler($response, $request);
        exit();
    }

    public function check()
    {
        echo "aka";
    }
    /**
     * Group 
     */
    public function group($path, $handler = [])
    {
        echo "\nGROUP =======>\n";
        // add wildcard to group path
        $currentPath = Uri::cleaner($path);
        $currentPath .= "/*";
        // path not matched
        echo "uri => ", $this->getRequestData("uri"), "\n";
        echo "path => ", $currentPath, "\n";
        if (!Uri::matchUriAndPath($this->getRequestData("uri"), $currentPath)) return $this;
        echo "match\n";
        // if (!Uri::matchUriAndPath($this->request_uri, $path)) return $this;

        for ($i = 0; $i < count($handler); $i++) {
            // echo $handler[$i];
            // check handler
            if (!is_callable($handler[$i]))
                throw new \Exception("handler is not callable");
            $handler[$i]("ID => " . $i);
        }
        echo "Handler len is " . count($handler) . " \n";

        // remove group path
        // $request_uri = ltrim(Uri::cleaner($this->request_uri), $currentPath);
        $request_url = $this->getRequestData("url");
        $request_method = $this->getRequestData("method");
        $request_uri = "/" . Uri::cleaner(ltrim(Uri::cleaner($this->getRequestData("uri")), $path));
        $previous_path = "/" . Uri::cleaner(Uri::cleaner($this->getConfig("previous_path") ?? "/") . "/" . Uri::cleaner($path));
        echo "request_url => ", $request_url, "\n";
        echo "request_method => ", $request_method, "\n";
        echo "request_uri => ", $request_uri, "\n";
        echo "previous_path => ", $previous_path, "\n";
        return new HttpPhp([
            "request_url" => $request_url,
            "request_method" => $request_method,
            "request_uri" => $request_uri,
            "previous_path" => $previous_path,
        ]);
    }
}
