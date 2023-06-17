<?php

namespace App;

class FileHandler
{
    protected $app_path;
    public File $file;

    public function __construct()
    {
        $this->app_path = $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * Get Form File
     * 
     * file_name => '$_FILES' key / "" to get all files 
     * 
     */
    public function get_from_files($file_name = "")
    {
        if ($file_name == "")
            return $_FILES;

        if (!isset($_FILES[$file_name]["name"]) || trim($_FILES[$file_name]["name"]) == "")
            return false;

        $this->file = new File([
            "name" => $_FILES[$file_name]["name"],
            "size" => $_FILES[$file_name]["size"],
            "path" => $_FILES[$file_name]["tmp_name"],
            "type" => $_FILES[$file_name]["type"],
        ]);
        return true;
    }

    /**
     * Get Saver File 
     * 
     * src => file path
     */
    public function get_server_files($src)
    {
        $file_path = join(DIRECTORY_SEPARATOR, [$this->app_path, $src]);

        if (!is_file($file_path) || !file_exists($file_path)) {
            // echo "File Not Found";
            return false;
        }

        $file_name = explode('/', $file_path);
        $file_name = end($file_name);

        $file_size   = filesize($file_path);

        $this->file = new File([
            "name" => $file_name,
            "size" => $file_size,
            "path" => $file_path,
            "type" => "",
        ]);
        return true;
    }

    /**
     * File Saver
     * 
     * dest => file path
     * 
     * name => new name
     */
    public function save(string $dest, string $name = "")
    {
        $dest_name = $this->file->get_name();
        if ($name != "") {
            $dest_name = $name;
        }

        $dest_path = join(DIRECTORY_SEPARATOR, [$this->app_path, $dest, $dest_name]);

        if (!move_uploaded_file($this->file->get_path(), $dest_path)) {
            throw new \Exception("File Save Failed");
        }

        // change file path
        $this->file->set_path($dest_path);
        $this->file->set_name($dest_name);

        return $this;
    }

    /**
     * Delete server file 
     * 
     * src => file path
     */
    public function delete(string $src)
    {
        $src_path = join(DIRECTORY_SEPARATOR, [$this->app_path, $src]);
        if (is_file($src_path))
            unlink($src_path);
    }

    /**
     * Forced Download 
     * 
     * name => file for download
     */
    public function download(string $name = "")
    {
        $file_name = $this->file->get_name();
        if ($name != "") {
            $file_name = $name;
        }

        $file_path = $this->file->get_path();

        if (!is_file($file_path) || !file_exists($file_path)) {
            echo "File Not Found";
            exit;
        }

        $file = file_get_contents($file_path);

        $file_size =  $this->file->get_size();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $file_size);
        echo $file;
    }
}


class File
{
    private $file_name = "";
    private $file_size = 0;
    private $file_path = "";
    private $file_type = "";

    function __construct($file_info = [
        "name" => "",
        "size" => 0,
        "path" => "",
        "type" => "",
    ])
    {
        $this->file_name = $file_info["name"];
        $this->file_size = $file_info["size"];
        $this->file_path = $file_info["path"];
        $this->file_type = $file_info["type"];
    }

    public function get_name()
    {
        return $this->file_name;
    }

    public function set_name(string $name)
    {
        $this->file_name = $name;
    }

    public function get_size()
    {
        return $this->file_size;
    }

    public function get_path()
    {
        return $this->file_path;
    }

    public function set_path(string $path)
    {
        $this->file_path = $path;
    }

    public function get_type()
    {
        return $this->file_type;
    }
}
