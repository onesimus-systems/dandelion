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
 * DandelionApplication represents an instance of Dandelion. It requires
 * the URI as a parameter and has one public method run(). The class parses
 * the URI, stores it and when run() is executed will route the request accordingly.
 */
class DandelionApplication {
    private $url = [];

    /**
     *  @param $url string - The request URI
     */
    public function __construct($url) {
        // Load installer if necassary
        if (!INSTALLED) {
            redirect('installer');
        }

        $this->url = $this->parseUrl($url);
    }

    /**
     * Parses given URI string and returns an array of the pieces
     *
     * @param $url string - Request URI
     * @return array - Exploded URL
     */
    private function parseUrl($url) {
        // Process url
        $url = $url ? $url : '';
        $url = explode('/', $url);
        array_shift($url); // Remove empty index at 0

        // Remove any GET parameters
        $url[count($url)-1] = explode('?', $url[count($url)-1])[0];
        return $url;
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run() {
        // Get the first element which is the page or command
        $page = array_shift($this->url);

        // Call the API if it's an api call
        if ($page == 'api') {
            $this->apiRequest();
            return;
        }

        $this->loadPage($page);
        return;
    }

    /**
     * Processes and api request
     */
    private function apiRequest() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $apiCall = new API\ApiController();
        $apiCall->processCall($this->url);
        return;
    }

    /**
     * Loads the given page
     *
     * @param $page string - Name of page to load
     */
    private function loadPage($page) {
        global $User_Rights;
        // Set the homepage if necassary
        if ($page === '') {
          $page = "viewlog";
        }

        // Load page
        $indexCall = true;
        if (is_file('pages/'.$page.'.php') && Gatekeeper\authenticated()) {
            include 'pages/'.$page.'.php';
        } else {
            include 'pages/login.php';
        }
        return;
    }

    /**
     * Gotta make sure the session is written and closed
     */
    public function __destruct() {
        session_write_close();
    }
}
