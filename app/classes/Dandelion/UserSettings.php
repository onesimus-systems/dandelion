<?php
/**
 * Manage user settings
 */
namespace Dandelion;

use \Dandelion\Storage\Contracts\DatabaseConn;

class UserSettings
{
    public function __construct(DatabaseConn $db)
    {
        $this->db = $db;
    }

    public function saveLogLimit($limit)
    {
        if ($limit < 5) {
          $limit = 5;
        } elseif ($limit > 500) {
          $limit = 500;
        }

        $this->db->update(DB_PREFIX.'users')
                 ->set('showlimit = :newlimit')
                 ->where('userid = :myID');
        $params = array(
            'newlimit' => $limit,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $this->db->go($params);

        $_SESSION['userInfo']['showlimit'] = $limit;

        return 'Show limit changed successfully';
    }

    public function saveTheme($theme)
    {
        $newTheme = isset($theme) ? $theme : 'default';

        $this->db->update(DB_PREFIX.'users')
                 ->set('theme = :theme')
                 ->where('userid = :myID');
        $params = array(
            'theme' => $newTheme,
            'myID' => $_SESSION['userInfo']['userid']
        );
        $this->db->go($params);

        $_SESSION['userInfo']['theme'] = $newTheme;

        return 'Theme saved successfully';
    }

    public function getSetting($setting, $id)
    {
        $this->db->select($setting)
                 ->from(DB_PREFIX.'users')
                 ->where('userid = :id');

        $params = array('id' => $id);
        return $this->db->get($params)[0][$setting];
    }
}
