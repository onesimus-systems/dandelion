<?php
/**
 * Controller to process authentication
 */
namespace Dandelion\Controllers;

use \Dandelion\UrlParameters;
use \Dandelion\Auth\GateKeeper;
use \Dandelion\Storage\MySqlDatabase;

class AuthController
{
    // Database connection
    private $db;

    // URL parameters
    private $up;

    public function __construct()
    {
        $this->db = MySqlDatabase::getInstance();
        $this->up = new UrlParameters();
    }

    public function login()
    {
        $auth = new GateKeeper($this->db);
        $rem = false;
        if ($this->up->remember == 'true') {
            $rem = true;
        }
        echo json_encode($auth->login($this->up->user, $this->up->pass, $rem));
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

        \Dandelion\redirect('index');
    }
}
