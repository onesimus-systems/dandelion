<?php
/**
 * Administation actions API module
 */
namespace Dandelion\API\Module;

use \Dandelion\AdminActions;
use \Dandelion\Controllers\ApiController;

class adminAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     *  Save the website tagline
     */
    public function saveSlogan()
    {
        return self::go("saveSlogan", $this->up->data);
    }

    /**
     *  Call DB backup function
     */
    public function backupDB()
    {
        return self::go("backupDB", $this->up->data);
    }

    /**
     *  Save the default theme for the site
     */
    public function saveDefaultTheme()
    {
        return self::go("saveDefaultTheme", $this->up->data);
    }

    /**
     *  Save Cheesto enabled state
     */
    public function saveCheesto()
    {
        return self::go("saveCheesto", $this->up->data);
    }

    /**
     *  Save public API enabled status
     */
    public function savePAPI()
    {
        return self::go("savePAPI", $this->up->data);
    }

    /**
     *  Perform administartor action
     */
    private function go($func, $data)
    {
        if (!$this->ur->isAdmin()) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'admin'));
        }

        $action = new AdminActions($this->db);
        $response = $action->$func($data);
        return $response;
    }
}
