<?php

namespace App\CLI;

use Exception;

class CommandLineHelper
{
    protected $command = "";
    protected $command_operation = "";
    protected $arg = [];

    public function __construct(array $argv)
    {
        $this->arg = $argv;
        $this->getCommandAndOperation($this->arg[1]);
        $this->execute_command();
    }

    function getCommandAndOperation(string $v)
    {
        // incase only command
        if (!preg_match_all("/:/", $v)) {
            $this->command = $v;
            return;
        }

        $temp = explode(':', $v);
        $this->command = $temp[0];
        $this->command_operation = $temp[1];
    }

    function execute_command()
    {
        switch ($this->command) {
            case 'create':
                $this->execute_command_operation();
                break;

            case 'serve':
                $this->start_php_server();
                break;

            case 'help':
                $this->print_commands();
                break;

            default:
                echo "Command Not Found";
                break;
        }
    }

    function execute_command_operation()
    {
        switch ($this->command_operation) {
            case 'feature':
                $this->create_feature();
                break;

            default:
                # code...
                break;
        }
    }

    function create_feature()
    {
        $feature_name = $this->arg[2];  // get feature name
        $feature_path = "Features/$feature_name/";  // prepare feature path
        $template_path = __DIR__ . "/templates/feature";

        mkdir($feature_path);  // create feature directory

        // fech templates file
        $template_files = scandir($template_path);

        for ($i = 2; $i < count($template_files); $i++) {
            // get file name
            $file_name = rtrim($template_files[$i], '.php') . "." . $feature_name . ".php";
            // get file content
            $file_content = file_get_contents($template_path . "/" . $template_files[$i]);
            // parse template
            $file_content = str_replace('feature_name', $feature_name, $file_content);
            // create files
            $f = fopen($feature_path . $file_name, "w")  or die("Unable to create file!");
            fwrite($f, $file_content);
            fclose($f);
        }
    }
    function start_php_server()
    {
        exec("php -S localhost:8000");
    }
    function print_commands()
    {
        $command_list_path = __DIR__."/command_list";
        echo file_get_contents($command_list_path);
    }
}
