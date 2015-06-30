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
            file_put_contents(self::$lockFile, Application::VERSION.PHP_EOL.Application::VER_NAME);
            return true;
        }

        if (self::needsUpdated()) {
            $app->logger->info('Redirecting to update controller');
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
            throw new \Exception('Version mismatch. Lock file is higher than application');
        } else {
            return true;
        }
    }
}
