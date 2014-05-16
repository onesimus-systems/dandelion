<?php
/**
 * Here be the install page for Dandelion.
 * Here is were the admin will enter database
 * type and credentials.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date March, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
session_start();

if (file_exists('../config/config.php')) {
    include_once '../config/config.php';

    if (!$CONFIG['installed']) {
        $needToInstall = true;

        if (!is_writable('../config/')) {
            $message = 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
        }
    } else {
        $needToInstall = false;
    }
} else {
    $needToInstall = true;
    if (!is_writable('../config/')) {
        $message = 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
    }
}

if ($needToInstall) {

?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <title>Dandelion Web Log - Install Script</title>
        <link rel="stylesheet" href="../styles/main.css" />
        <link rel="stylesheet" type="text/css" href="../themes/Halloween/main.css" />
        <link rel="icon" type="image/ico" href="../images/favicon.ico" />
    </head>

    <body>
        <br /><br />
        <h1>Install Dandelion Web Log</h1>

        <p>The following information is needed to install Dandelion:</p>

        <?php
        if (!is_null($message)) {
            echo $message;
        } else {?>
        <form method="post" class="le" action="install.php">
            <h2>Database Information</h2>

            <?php
                if (isset($_SESSION['error_text'])) {
                    echo '<h3>'.$_SESSION['error_text'].'</h3>';
                    unset($_SESSION['error_text']);
                }
            ?>

            <table>
                <tr>
                    <td>Database Type:</td>
                    <td><select name="dbtype" onChange="showHide(this.value);"><option value="mysql">MySQL</option><option value="sqlite">SQLite</option></select></td>
                </tr>
            </table>
            <table id="mysql_only" style="display:inline;">
                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="dbuname" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="dbpass" /></td>
                </tr>
                <tr>
                    <td>Host/IP Address:</td>
                    <td><input type="text" name="dbhost" /></td>
                </tr>
                <tr>
                    <td>Database Name:</td>
                    <td><input type="text" name="dbname" /></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Dandelion Path:</td>
                    <td><input type="text" name="danPath"> (http://host.com/folder)</td>
                </tr>
            </table>
            <table>
                <tr>
                   <td><input type="submit" value="Finish Install" /></td>
                </tr>
            </table>
        </form>
        <?php } ?>
    </body>

    <script type="text/javascript">
    public function showHide(db_type)
    {
        if (db_type == 'sqlite') {
            document.getElementById('mysql_only').style.display = 'none';
        } else {
            document.getElementById('mysql_only').style.display = 'inline';
        }
    }
    </script>
</html>
<?php
} else {
    header( 'Location: ../' );
}
?>
