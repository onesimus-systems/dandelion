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

use \Dandelion\Repos\Interfaces\AdminRepo;

class AdminActions
{
    public function __construct(AdminRepo $repo)
    {
        $this->repo = $repo;
    }
}
