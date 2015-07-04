<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Utils;

use Dandelion\Application;
use Dandelion\Session\SessionManager as Session;

class Updater
{
    /**
     * Filename of lock file
     * @var string
     */
    private static $lockFile;

    /**
     * Called to initiate an update sequence. Redirects to update page if needed
     *
     * @param  Application $app
     * @return bool
     */
    public static function checkForUpdates(Application $app)
    {
        self::$lockFile = $app->paths['app'].'/update.lock';

        if (!file_exists(self::$lockFile)) {
            self::writeUpdateLockFile();
            return true;
        }

        if (self::needsUpdated() && Session::get('updateInProgress') !== true) {
            $app->logger->info('Redirecting to update controller');
            Session::set('updateInProgress', true);
            View::redirect('update');
        }
        return true;
    }

    /**
     * Determine if the current installation needs to be updated
     *
     * @return bool
     */
    public static function needsUpdated()
    {
        $lockVersionFile = file(self::$lockFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $versionNum = $lockVersionFile[0];
        $versionName = $lockVersionFile[1];

        if (version_compare($versionNum, Application::VERSION, '=')) {
            return false;
        } elseif (version_compare($versionNum, Application::VERSION, '>')) {
            self::writeUpdateLockFile();
        } else {
            return true;
        }
    }

    /**
     * Write a lock file with the current version number and name.
     *
     * @return bool
     */
    public static function writeUpdateLockFile()
    {
        return (bool) file_put_contents(self::$lockFile, Application::VERSION.PHP_EOL.Application::VER_NAME);
    }
}
