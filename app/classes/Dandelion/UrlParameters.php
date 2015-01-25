<?php
/**
 * Encapsulation of URL parameters
 */
namespace Dandelion;

class UrlParameters
{
    // List of URL parameters
    private $values = [];

    /**
     * Create object using $type of url arguments
     *
     * @param $type string - Type of URL parameters to store: get, post, request (default)
     */
    public function __construct($type = 'request')
    {
        switch($type) {
            case 'request':
                $this->values = $_REQUEST;
                break;
            case 'post':
                $this->values = $_POST;
                break;
            case 'get':
                $this->values = $_GET;
                break;
        }
    }

    /**
     * Magic method to get a parameter. Calls public get() method with default as null
     *
     * @param $name string - Name of parameter to retrieve
     * @return mixed - Value of parameter or null
     */
    public function __get($name)
    {
        return $this->get($name, null);
    }

    /**
     * Checks if $name exists in parameter list and returns value or $default
     *
     * @param $name string - Name of parameter to retrieve
     * @return mixed - Value of parameter or $default
     */
    public function get($name, $default = '')
    {
        if (array_key_exists($name, $this->values)) {
            return urldecode($this->values[$name]);
        } else {
            return $default;
        }
    }
}
