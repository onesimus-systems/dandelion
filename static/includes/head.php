<?php
namespace Dandelion;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>" />
        <?php echo loadCssSheets("cheesto","jqueryui","tutorial","permissions.css","datetimepicker.css","mail"); ?>
        <title>Dandelion Web Log</title>
    </head>
    <body>
        <header>
            <h1 class="t_cen"><?php echo $_SESSION['app_settings']['app_title']; ?></h1>
            <h4 class="t_cen"><?php echo $_SESSION['app_settings']['slogan']; ?></h4>

            <nav class="t_cen" id="nav_link">
                <a href="./">View Log</a><a href="settings">Settings</a><a href="admin">Administration</a><a href="tutorial">Tutorial</a><a href="logout">Logout</a>
            </nav>
        </header>