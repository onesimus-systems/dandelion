<?php
/**
 *
 *
 */

namespace Dandelion;

class userSettings {
    public function __construct($db) {
        $this->db = $db;
    }

    public function saveLogLimit($limit) {
        if ($limit < 5 || $limit > 500) {
            return 'Please choose a number between 5 and 500.';
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

    public function saveTheme($theme) {
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

    public function resetPassword($password) {
        $user = new Users\User(false, $_SESSION['userInfo']['userid']);
        $user->userInfo['password'] = $password;
        return $user->resetUserPw();
    }

    public function getSetting($setting, $id) {
        $this->db->select($setting)
                 ->from(DB_PREFIX.'users')
                 ->where('userid = :id');

        $params = array('id' => $id);
        return $this->db->get($params)[0][$setting];
    }
}
