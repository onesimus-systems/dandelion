<?php
/**
  * This is the main entry point of Dandelion. All page and API requests are routed here.
  *
  * This file is a part of Dandelion
  *
  * @author Lee Keitel
  * @date Janurary 23
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
namespace Dandelion;

require_once 'lib/bootstrap.php';

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
        array_shift($url); // Remove empty index
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

// Instantiate and run application
$app = new DandelionApplication($_SERVER['REQUEST_URI']);
$app->run();
