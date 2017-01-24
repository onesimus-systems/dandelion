<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

$configDir = __DIR__.'/../../config';

$config = [
    'db' => [
        'type' => $_POST['db_type'] ?: '',
        'dbname' => $_POST['db_name'] ?: '',
        'hostname' => $_POST['db_host'] ?: '',
        'username' => $_POST['db_user'] ?: '',
        'password' => $_POST['db_pass'] ?: '',
        'tablePrefix' => $_POST['db_prefix'] ?: 'dan_',
    ],

    'hostname' => $_POST['hostname'] ? rtrim($_POST['hostname'], '/') : 'http://localhost',
    'cookiePrefix' => $_POST['cookie_prefix'] ?: 'dan_',
    'installed' => true,
    'appTitle' => $_POST['apptitle'] ?: 'Dandelion Web Log',
    'tagline' => $_POST['tagline'] ?: ''
];

try {
    if (!is_writable($configDir)) { // Is it possible to write the config file?
        throw new \Exception('Dandelion does not have sufficient write permissions to create configuration.<br />Please make the app/config directory writeable to Dandelion and try again.');
    }

    switch ($config['db']['type']) {
        case 'mysql':
            if ($config['db']['hostname'] && $config['db']['dbname']) {
                $db_connect = "mysql:host={$config['db']['hostname']};dbname={$config['db']['dbname']}";
            } else {
                throw new \Exception('No hostname or database name specified');
            }
            break;

        case 'sqlite':
            $config['db']['hostname'] = $configDir.'/dandelion.sqlite';
            // Truncate file if exists
            $f = @fopen($config['db']['hostname'], "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $db_connect = "sqlite:{$config['db']['hostname']}";
            break;

        default:
            throw new \Exception('No database driver loaded.');
            break;
    }

    $conn = new \PDO($db_connect, $config['db']['username'], $config['db']['password']);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__."/{$config['db']['type']}_schema_template.tmpl");

    // Replace the prefix placeholder with the user defined prefix
    $sql = str_replace('{{prefix}}', $config['db']['tablePrefix'], $sql);

    $success = false;
    if ($config['db']['type'] === 'sqlite') {
        // SQLite doesn't like multiple statements in a single query
        // so the file is separated by double semicolons for each
        // statement in order to separate them and run them individually.
        // MySQL doesn't have an issue with this.
        $queries = explode(';;', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if ($query) {
                $success = $conn->exec($query);
            }
        }
    } else { // MySQL
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute();
        while ($stmt->nextRowset()) {
            /* https://bugs.php.net/bug.php?id=61613 */
        };
    }

    if (!$success) {
        throw new \Exception('Problem installing initial data into database.');
    }

    $conn = null;

    // Save as new configuration file
    $banner = '// This file was generated on '.date(DATE_RFC2822);
    $configFile = '<?php '.$banner.PHP_EOL.'$config = '.var_export($config, true).';';
    file_put_contents($configDir.'/config.php', $configFile);

    session_destroy();
    $_SESSION['error'] = 'Dandelion has been successfully setup! Please go to: <a href="'.$config['hostname'].'">'.$config['hostname'].'</a>';
    header("Location: {$config['hostname']}");
} catch (\PDOException $e) {
    $_SESSION['error'] = 'Error setting up database. Please verify database name, address, username, and password. '.$e->getMessage();
} catch (\Exception $e) {
    $_SESSION['error'] = 'Error: '.$e->getMessage();
}
