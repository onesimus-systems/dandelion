<?php
/**
 * Controller to process authentication
 */
namespace Dandelion\Controllers;

use \Dandelion\Utils\View;
use \Dandelion\Application;
use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;
use \League\Plates\Engine;

class AuthController extends BaseController
{
    // Database connection
    private $db;

    // URL parameters
    private $up;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->db = MySqlDatabase::getInstance();
        $this->up = new UrlParameters();
    }

    public function loginPage()
    {
        if (GateKeeper::authenticated()) {
            View::redirect('dashboard');
            return;
        }

        $templates = new Engine($this->app->paths['app'].'/templates');
        $templates->registerFunction('getCssSheets', function() {
            return View::loadCssSheets();
        });
        $templates->registerFunction('loadJS', function() {
            return View::loadJS('jquery','login');
        });

        echo $templates->render('login');
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
            View::redirect('login');
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

        View::redirect('login');
    }
}
