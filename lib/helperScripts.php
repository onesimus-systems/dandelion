<?php
/**
 * Dandelion helper functions for core.
 *
 * @author Lee Keitel
 *
 * @license GNU GPLv3
 */

namespace Dandelion {

function redirect($pagename) {
    $allPages = array(
        'home' => '',
        'homepage' => '',
        'index' => '',
        'userSettings' => 'settings',
        'adminSettings' => 'admin',
        'tutorial' => 'tutorial',
        'logout' => 'logout',
        'about' => 'about',
        'adminCategories' => 'categories',
        'adminGroups' => 'editgroups',
        'adminUsers' => 'editusers',
        'installer' => 'install/index.php',
        'mailbox' => 'mail',
        'resetPassword' => 'reset',
    );

    if (!array_key_exists($pagename, $allPages)) {
        trigger_error($pagename . ' is not an available redirect page.');
        return;
    }

    $newPath = HOSTNAME . '/' . $allPages[$pagename];
    header("Location: $newPath");
    return;
}
}


namespace Dandelion\Gatekeeper {
/**
 * Simple function to determine if a user is logged in or not.
 *
 * @return bool
 */
function authenticated()
{
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
    return $loggedin;
}

/**
 * Perform a logout by destroying the session.
 */
function logout()
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
