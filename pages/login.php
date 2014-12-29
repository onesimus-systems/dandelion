<?php
namespace Dandelion;

if (!$indexCall) {
    header('Dandelion: Access Denied');
    exit(1);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>">
        <?php echo loadCssSheets(); ?>
        <title>Dandelion Web Log</title>
    </head>
    <body>
        <div id="login">
            <h1>Dandelion Web Log</h1>
            <form id="login_form">
                Username:<br><input type="text" value="" id="username" onKeyPress="check(event);" autocomplete="off"><br><br>
                Password:<br><input type="password" value="" id="password" onKeyPress="check(event);"><br>
                <br><label id="remember"><input type="checkbox" value="remember" id="rememberMe">Remember my username</label><br>
                <div style="margin-top: 0em; margin-left: 2em;">
                    <button type="button" id="login_button" onClick="attemptLogin();">Login</button>
                </div>
            </form>
        </div>
    </body>
    <?php echo loadJS('jquery', 'login.js'); ?>
</html>
