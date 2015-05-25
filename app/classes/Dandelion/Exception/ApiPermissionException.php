<?php
/**
 * Exception class for API for insufficient permissions
 */
namespace Dandelion\Exception;

class ApiPermissionException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Your account doesn\'t have the proper permissions', 4);
    }
}
