<?php
namespace Dandelion\Controllers;

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
        if (is_file('pages/'.$page.'.php') && \Dandelion\Gatekeeper\authenticated()) {
            include 'pages/'.$page.'.php';
        } else {
            include 'pages/login.php';
        }
        return;
    }
}
