<?php
/**
 *  Base module extended by all API modules
 */
namespace Dandelion\API\Module;

use \Dandelion\Controllers\ApiController;
use \Dandelion\Storage\Contracts\DatabaseConn;

abstract class BaseModule
{
    // Database connection
    protected $db;

    // User rights
    protected $ur;

    // URL parameters
    protected $up;

    public function __construct(DatabaseConn $db, $ur, $urlParameters) {
        $this->db = $db;
        $this->ur = $ur;
        $this->up = $urlParameters;
    }
}
