<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

class UrlParameters
{
    // List of URL parameters
    private $values = [];

    /**
     * Create object using $type of url arguments
     *
     * @param $type string - Type of URL parameters to store: get, post, both (default, empty)
     */
    public function __construct($type = '')
    {
        switch($type) {
            case 'post':
                $this->values = $_POST;
                break;
            case 'get':
                $this->values = $_GET;
                break;
            default:
                $this->values = array_merge($_POST, $_GET);
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
