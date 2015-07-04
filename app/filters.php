<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Utils\View;
use Dandelion\Auth\GateKeeper;
use Dandelion\Utils\Configuration;
use Dandelion\Session\SessionManager as Session;

use Onesimus\Router\Router;

// Used on page requests, redirects to login page if not logged in
Router::filter('auth', function() {
    if (GateKeeper::authenticated()) {
        return true;
    } else {
        View::redirect('login');
        return false;
    }
});

// Sets and checks a lastAccessed time on the session. This filter is only
// used for page requests. Where this filter matters, the auth filter is called
// afterwards which will redirect to the login page
Router::filter('sessionLastAccessed', function() {
    if (Session::get('lastAccessed', false)) {
        $config = Configuration::getConfig();
        $timeout = $config['sessionTimeout']*60;
        $now = time();
        if ((Session::get('lastAccessed') + $timeout) < $now) {
            GateKeeper::logout();
            return true;
        }
    }
    Session::set('lastAccessed', time());
    return true;
});

// Used for internal api requests. Only checks for and logs out use. This
// filter does not update a lastAccessed timestamp. The controller will return
// an errorcode indicating login required.
Router::filter('apiSessionLastAccessed', function() {
    if (Session::get('lastAccessed', false)) {
        $config = Configuration::getConfig();
        $timeout = $config['sessionTimeout']*60;
        $now = time();
        if ((Session::get('lastAccessed') + $timeout) < $now) {
            GateKeeper::logout();
        }
    }
    return true;
});
