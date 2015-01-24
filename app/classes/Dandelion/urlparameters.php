<?php
/**
 * Encapsulation of URL parameters.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Jan 2015
 */
namespace Dandelion;

class UrlParameters {
    // List of URL parameters
    private $values = [];

    /**
     * Create object using $type of url arguments
     *
     * @param $type string - Type of URL parameters to store: get, post, request (default)
     */
    public function __construct($type = 'request') {
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
     * Magic method to get a parameter. Calls public get() method with default as NULL
     *
     * @param $name string - Name of parameter to retrieve
     * @return mixed - Value of parameter or NULL
     */
    public function __get($name) {
        return $this->get($name, NULL);
    }

    /**
     * Checks if $name exists in parameter list and returns value or $default
     *
     * @param $name string - Name of parameter to retrieve
     * @return mixed - Value of parameter or $default
     */
    public function get($name, $default = '') {
        if (array_key_exists($name, $this->values)) {
            return urldecode($this->values[$name]);
        } else {
            return $default;
        }
    }
}
