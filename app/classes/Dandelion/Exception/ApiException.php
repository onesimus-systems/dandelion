<?php
/**
 * Exception class for the API
 */
namespace Dandelion\Exception;

class ApiException extends \Exception
{
    protected $module;

    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setModule($module = 'api')
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }
}
