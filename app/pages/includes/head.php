<?php
/**
 * Global head and header file
 */
namespace Dandelion;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="/assets/favicon.ico" />
        <?php
        if (!isset($requiredCssFiles)) {
            // Default CSS list, only the main styles
            $requiredCssFiles = array();
        }
        echo call_user_func_array(array('\Dandelion\Utils\View', 'loadCssSheets'), $requiredCssFiles);?>
        <title>Dandelion Web Log</title>
    </head>
    <body>
        <header>
            <h1 class="t_cen"><?= 'Application Title'; ?></h1>
            <h4 class="t_cen"><?= 'Application Tagline'; ?></h4>

            <nav class="t_cen" id="nav_link">
                <a href="/">Dashboard</a><a href="/settings">Settings</a><a href="/admin">Administration</a><a href="/tutorial">Tutorial</a><a href="/logout">Logout</a>
            </nav>
        </header>
