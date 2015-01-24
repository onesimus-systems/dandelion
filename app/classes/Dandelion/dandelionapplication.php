<?php
/**
  * Dandelion Application class
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  * The full GPLv3 license is available in LICENSE.md in the root.
  *
  * @author Lee Keitel
  * @date Jan 2015
***/
namespace Dandelion;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class DandelionApplication {
    private $url = [];

    /**
     *  @param $url string - The request URI
     */
    public function __construct() {
        // Load installer if necassary
        if (!INSTALLED) {
            redirect('installer');
        }
        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run() {
        \Dandelion\Routes::route($this->url);
        return;
    }

    /**
     * Gotta make sure the session is written and closed
     */
    public function __destruct() {
        session_write_close();
    }
}
