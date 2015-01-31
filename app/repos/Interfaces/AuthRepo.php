<?php
/**
 * Created by PhpStorm.
 * User: lfkeitel
 * Date: 1/31/15
 * Time: 1:22 PM
 */
namespace Dandelion\Repos\Interfaces;

interface AuthRepo
{
    public function isUser($username);
}
