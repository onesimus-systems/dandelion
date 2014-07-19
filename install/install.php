<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $CONFIG = array(
        'db_type' => $_POST['dbtype'],
        'db_user' => $_POST['dbuname'],
        'db_pass' => $_POST['dbpass'],
        'db_host' => $_POST['dbhost'],
        'db_name' => $_POST['dbname']
    );

    $hostname = rtrim($_POST['danPath'], "/");

    try {
        if (is_writable('../config')) { // Is it possible to write the config file?

            switch($CONFIG['db_type']) {
                case 'mysql':
                    $db_connect = (!empty($CONFIG['db_host']) && !empty($CONFIG['db_name'])) ? 'mysql:host='.$CONFIG['db_host'].';dbname='.$CONFIG['db_name'] : '';
                    $db_user = $CONFIG['db_user'];
                    $db_pass = $CONFIG['db_pass'];
                    $sqliteFileName = '';
                    break;

                case 'sqliteDISABLED':
                    $db_unique_filename = mt_rand(1, 100); // To prevent overwriting an old database, generate a random number as a unique identifier
                    if (!is_dir(dirname(dirname(__FILE__)).'/database')) {
                        mkdir(dirname(dirname(__FILE__)).'/database');
                    }
                    $sqliteFileName = 'database'.$db_unique_filename.'.sq3';
                    $db_connect = 'sqlite:'.dirname(dirname(__FILE__)).'/database/'.$sqliteFileName;
                    $db_user = null;
                    $db_pass = null;
                    break;

                default:
                    throw new Exception('Error: No database driver loaded');
                    break;
            }

            if ($db_connect === '') {
                $_SESSION['error_text'] = 'Please enter MySQL database connection information:';
                header( 'Location: ../install.php' );
            }

            $conn = new PDO($db_connect, $db_user, $db_pass);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            include_once $CONFIG['db_type'].'Install.php'; // Load the database specific creation commands

            $conn = null;

            /** Write config file */
            $newFile = "<?php
\$DBCONFIG = array (
'db_type' => '{$CONFIG['db_type']}',
'sqlite_fn' => '',
'db_name' => '{$CONFIG['db_name']}',
'db_host' => '{$CONFIG['db_host']}',
'db_user' => '{$CONFIG['db_user']}',
'db_pass' => '{$CONFIG['db_pass']}',
'db_prefix' => 'dan_'
);

define('HOSTNAME', '{$hostname}');
define('PHP_SESSION_NAME', 'dan_session_1');
define('DEBUG_ENABLED', true);
define('INSTALLED', true);";

            file_put_contents('../config/config.php', $newFile);

            // Change config directory to user:readonly for security
            chmod('../config/config.php', 0400);
            chmod('../config', 0500);

            header( 'Location: ../lib/logout.php' );
        } else {
            echo 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
        }
    } catch(PDOException $e) {
        echo 'Error setting up database: '.$e;
    }
} else {
    header( 'Location: ../install.php' );
}
