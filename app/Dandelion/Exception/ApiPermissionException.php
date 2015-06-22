<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Exception;

class ApiPermissionException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Your account doesn\'t have the proper permissions', 4);
    }
}
