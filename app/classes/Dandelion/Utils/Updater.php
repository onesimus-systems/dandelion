<?php
/**
 * Applies update if update.lock is present in the root directory.
 *
 * Notes: The update.lock file will be created by the installer
 *
 * @author Lee Keitel
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 */

namespace Dandelion\Utils;

class Updater
{
    public static function checkForUpdate() {
        $updateLockFile = ROOT . '/update.lock';
        if (!file_exists($updateLockFile)) {
            return 'This instance of Dandelion isn\'t configured for automatic updates.';
        }

        $oldVersion = file_get_contents($updateLockFile);
        if ($oldVersion == D_VERSION) {
            return 'This instance of Dandelion is already up to date.';
        }

        // Show message about updates

        // Run update sequence for each version from $oldVersion to D_VERSION

        // if (update sequence returns true) {
            file_put_contents($updateLockFile, D_VERSION);
            // Show message about successful updates
            exit(0);
        // } else {
        //  reportError();
        // }
        return;
    }
}
