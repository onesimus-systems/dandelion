<?php
/**
 * Main Dandelion application
 */
namespace Dandelion\Controllers;

use \Dandelion\Routes;

/**
 * DandelionApplication represents an instance of Dandelion.
 */
class AppController
{
    private $url;

    /**
     *  @param $url string - The request URI
     */
    public function __construct()
    {
        // Load installer if necassary
        if (!INSTALLED) {
            \Dandelion\redirect('installer');
        }
        $this->url = $_SERVER['REQUEST_URI'];
    }

    /**
     * Main function of this class and single entrypoint into application.
     * Run takes the parsed URL and routes it to the appropiate place be it
     * the api controller or a page.
     */
    public function run()
    {
        include ROOT.'/routes.php';
        Routes::route($this->url);
        return;
    }
}
