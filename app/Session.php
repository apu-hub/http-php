<?php
namespace App;

class Session
{
    protected $prefix = "";
    protected $session_data = [];

    public function __construct(string $prefix = "")
    {
        $this->prefix = $prefix;
        $this->get_session_data();
    }

    // add a local session var
    public function get_session_data()
    {
        $this->session_data = $_SESSION;
    }

    public function has(string $key)
    {
        $value = trim($this->session_data[$key] ?? "");

        if ($value != "") {
            return true;
        }

        foreach ($this->session_data as $prefix => $val) {
            $value = trim($val[$key] ?? "");
            if ($value != "") {
                $this->prefix = $prefix;
                return true;
            }
        }

        return false;
    }

    public function get(string $key, $default = "")
    {
        $value = trim($this->session_data[$key] ?? "");
        if ($value != "") {
            return $value;
        }

        $value = trim($this->session_data[$this->prefix][$key] ?? "");
        if ($value != "") {
            unset($_SESSION[$this->prefix]);
            return $value;
        }

        return $default;
    }

    public function with(string $key, $value)
    {
        // clean all
        unset($_SESSION[$this->prefix]);

        // set new group
        $_SESSION[$this->prefix][$key] = $value;
    }
}
