<?php
if (file_exists('config/config.php')) {
    include_once 'config/config.php';
    
    if (!$CONFIG['installed']) {
        $needToInstall = true;
        
        if (!is_writable('config/')) { // Is it possible to write the config file?
            $message = 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
        }
    }
    else {
        $needToInstall = false;
    }
}
else {
    $needToInstall = true;
    if (!is_writable('config/')) { // Is it possible to write the config file?
        $message = 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
    }
}

if ($needToInstall) {
?>
    <html>
        <head>
            <meta charset="utf-8" />
            <title>Dandelion Web Log - Install Script</title>
            <link rel="stylesheet" href="styles/main.css" />
            <link rel="icon" type="image/ico" href="images/favicon.ico" />
        </head>
        
        <body>
            <br /><br />
            <h1>Install Dandelion Web Log</h1>
            
            <p>The following information is needed to install Dandelion:</p>
            
            <?php
            if (!is_null($message)) {
                echo $message;
            }
            else {?>
            <form method="post" class="le" action="install/install.php">
                <h2>Database Information</h2>
                
                <table>
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
                    <tr>
                        <td><input type="submit" value="Finish Install" /></td>
                    </tr>
                </table>
            </form>
            <?php } ?>
        </body>
    </html>
<?php
}
else {
    header( 'Location: viewlog.phtml' );
}
?>