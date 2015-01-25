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
        'dashbaord' => '',
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
