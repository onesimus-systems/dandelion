<?php
/**
 * Demo controller
 */
namespace Dandelion\Controllers;

use \Dandelion\View;
use \Dandelion\Application;

class TutorialController
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function run()
    {
        $page = new View();
        $page->setTemplatesDirectory($this->app->paths['app'].'/pages');
        $page->display('tutorial.php', array('includes' => $this->app->paths['app']));
    }
}
