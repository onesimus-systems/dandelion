<?php
/**
 * Dandelion helper functions for core.
 *
 * @author Lee Keitel
 * 
 * @license GNU GPLv3
 */

namespace Dandelion;

function redirect($pagename) {
    $allPages = array(
        'home' => '',
        'homepage' => '',
        'index' => '',
        'userSettings' => 'settings.php',
        'adminSettings' => 'admin.php',
        'tutorial' => 'tutorial.php',
        'logout' => 'lib/logout.php',
        'about' => 'about.php',
        'adminCategories' => 'categories.php',
        'adminGroups' => 'editgroups.php',
        'adminUsers' => 'editusers.php',
        'installer' => 'install/index.php',
        'mailbox' => 'mailbox.php',
        'resetPassword' => 'reset.php',
    );

    if (!array_key_exists($pagename, $allPages)) {
        trigger_error($pagename . ' is not an available redirect page.');
        return;
    }

    $newPath = HOSTNAME . '/' . $allPages[$pagename];
    header("Location: $newPath");
    return;
}
