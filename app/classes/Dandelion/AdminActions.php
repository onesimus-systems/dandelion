<?php
/**
 * Administration Actions
 */
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class AdminActions
{
    public function __construct(DatabaseConn $db)
    {
        $this->dbConn = $db;
    }

    /**
     * Save the website tagline
     *
     * @param $data string - Website slogan
     * @return string
     */
    public function saveSlogan($data)
    {
        // Save new slogan
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :slogan')
                      ->where('name = "slogan"');
        $params = array(
            'slogan' => urldecode($data)
        );
        $this->dbConn->go($params);

        $_SESSION['app_settings']['slogan'] = urldecode($data);

        return 'Slogan set successfully';
    }

    /**
     * Call DB backup function
     *
     * @return string
     */
    public function backupDB()
    {
        $saveMe = new BackupDb($this->dbConn);
        return $saveMe->doBackup();
    }

    /**
     * Save the default theme for the site
     *
     * @param $data string - Theme name
     * @return string
     */
    public function saveDefaultTheme($data)
    {
        // Set new default theme
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :theme')
                      ->where('name = "default_theme"');
        $params = array(
            'theme' => $data
        );
        $this->dbConn->go($params);

        return 'Default theme set successfully';
    }

    /**
     * Save Cheesto enabled state
     *
     * @param $data bool - Enabled?
     * @return string
     */
    public function saveCheesto($data)
    {
        // Set cheesto enabled/disabled
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :enabled')
                      ->where('name = "cheesto_enabled"');
        $enabled = ($data == 'true') ? 1 : 0;
        $params = array(
            'enabled' => $enabled
        );
        $this->dbConn->go($params);

        return 'Settings set successfully';
    }

    /**
     * Save public API enabled status
     *
     * @param $data bool - Enabled?
     * @return string
     */
    public function savePAPI($data) {
        // Set Public API enabled/disabled
        $this->dbConn->update(DB_PREFIX.'settings')
                      ->set('value = :enabled')
                      ->where('name = "public_api"');
        $enabled = ($data == 'true') ? 1 : 0;
        $params = array(
            'enabled' => $enabled
        );
        $this->dbConn->go($params);

        return 'Setting saved';
    }
}
