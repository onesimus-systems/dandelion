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
        <?= $this->getCssSheets() // Special function defined in auth controller ?>
        <title>Dandelion Web Log - Login</title>
    </head>
    <body>
        <div class="login-box">
            <h1>Dandelion Login</h1>

            <form>
                <fieldset>
                    <label for="username">Username:</label>
                    <div class="textfield"><input type="text" id="username"></div>
                </fieldset>

                <fieldset>
                    <label for="password">Password:</label>
                    <div class="textfield"><input type="password" id="password"></div>
                </fieldset>

                <fieldset>
                    <strong>Remember username:</strong> <input type="checkbox" id="remember-username">
                </fieldset>

                <fieldset>
                    <button type="button" class="button" id="login-btn">Login</button>
                </fieldset>
            </form>
        </div>
        <?= $this->loadJS() // Special function defined in auth controller ?>
    </body>
</html>
