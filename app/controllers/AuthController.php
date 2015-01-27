<?php
/**
 * Controller to process authentication
 */
namespace Dandelion\Controllers;

use \Dandelion\View;
use \Dandelion\Utils\View as Vutils;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

class AuthController
{
    // Database connection
    private $db;

    // URL parameters
    private $up;

    public function __construct(Application $app)
    {
        $this->db = MySqlDatabase::getInstance();
        $this->up = new UrlParameters();
        $this->app = $app;
    }

    public function loginPage()
    {
        if (GateKeeper::authenticated()) {
            Vutils::redirect('dashboard');
            return;
        }

        $template = new View();
        $template->setTemplatesDirectory($this->app->paths['app'].'/templates');
        $template->display('login.php');
    }

    public function login()
    {
        $auth = new GateKeeper($this->db);
        $rem = false;
        if ($this->up->remember == 'true') {
            $rem = true;
        }

        $tryAuth = $auth->login($this->up->user, $this->up->pass, $rem);
        if (!$tryAuth) {
            Vutils::redirect('login');
        } else {
            echo json_encode($tryAuth);
        }
        return;
    }

    public function logout()
    {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        Vutils::redirect('login');
    }
}
