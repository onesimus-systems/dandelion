<!--
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="assets/favicon.ico">

        <?= $this->loadCss(['jqueryui', 'login']) ?>
        <title>Dandelion Web Log - Login</title>
    </head>
    <body>
        <div class="login-box">
            <h1>Dandelion</h1>

            <form>
                <fieldset>
                    <div class="textfield"><input type="text" id="username" placeholder="Username"></div>
                </fieldset>

                <fieldset>
                    <div class="textfield"><input type="password" id="password" placeholder="Password"></div>
                </fieldset>

                <fieldset>
                    <div class="remember"><strong>Remember username:</strong> <input type="checkbox" id="remember-username"></div>

                    <button type="button" class="button" id="login-btn">Log in</button>
                </fieldset>
            </form>
        </div>
        <?= $this->loadJS(['jquery', 'jqueryui', 'login']) ?>
    </body>
</html>
