<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="assets/favicon.ico">
        <?= $this->getCssSheets() // Special function defined in auth controller ?>
        <title>Dandelion Web Log - Login</title>
    </head>
    <body>
        <div id="login">
            <h1>Dandelion Web Log</h1>
            <form id="login_form">
                Username:<br><input type="text" value="" id="username" onKeyPress="check(event);" autocomplete="off"><br><br>
                Password:<br><input type="password" value="" id="password" onKeyPress="check(event);"><br>
                <br><label id="remember"><input type="checkbox" value="remember" id="rememberMe">Remember my username</label><br>
                <div class="login_button_div">
                    <button type="button" id="login_button" onClick="attemptLogin();">Login</button>
                </div>
            </form>
        </div>
    </body>
    <?= $this->loadJS() // Special function defined in auth controller ?>
</html>
