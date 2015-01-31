<?php
/**
 * Administration Actions
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\AdminRepo;

class AdminActions
{
    public function __construct(AdminRepo $repo)
    {
        $this->repo = $repo;
    }
}
