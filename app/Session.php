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
        if (isset($this->session_data[$key])) {
            return true;
        }

        foreach ($this->session_data as $prefix => $val) {
            if (isset($val[$key])) {
                $this->prefix = $prefix;
                return true;
            }
        }

        return false;
    }

    public function runtime(string $key, $default = "")
    {
        if (isset($this->session_data[$key])) {
            unset($_SESSION[$key]);
            return $this->session_data[$key];
        }

        if (isset($this->session_data[$this->prefix][$key])) {
            unset($_SESSION[$this->prefix][$key]);
            return $this->session_data[$this->prefix][$key];
        }

        return $default;
    }

    public function get(string $key, $default = "")
    {
        if (isset($this->session_data[$key])) {
            return $this->session_data[$key];
        }

        if (isset($this->session_data[$this->prefix][$key])) {
            return $this->session_data[$this->prefix][$key];
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

    public function set(string $key, $value)
    {
        // set new group
        $_SESSION[$this->prefix][$key] = $value;
    }
    public function destroy()
    {
        unset($_SESSION[$this->prefix]);
    }
}
