<?php
namespace Dandelion\Controllers;

use \Dandelion\UrlParameters;
use \Dandelion\Categories;
use \Dandelion\Storage\MySqlDatabase;

class DefaultPageController
{
    public function render($page) {
        global $User_Rights;
        // Set the homepage if necassary
        if ($page === '') {
          $page = "viewlog";
        }

        // Load page
        $indexCall = true;
        if (is_file(ROOT.'/pages/'.$page.'.php') && \Dandelion\Gatekeeper\authenticated()) {
            include ROOT.'/pages/'.$page.'.php';
        } else {
            include ROOT.'/pages/login.php';
        }
        return;
    }

    public function categories() {
        $urlParams = new UrlParameters();

        if ($urlParams->action == 'grabcats') {
            $past = json_decode(stripslashes($urlParams->pastSelections));
            $displayCats = new Categories(MySqlDatabase::getInstance());
            echo $displayCats->getChildren($past);
        }
        return;
    }
}
