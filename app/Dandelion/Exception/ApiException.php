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

class ApiException extends \Exception
{
    protected $module;
    protected $httpCode;
    protected $internalMsg;

    public function __construct($message = '', $code = 0, $httpCode = 200, $internal = '')
    {
        parent::__construct($message, $code);
        if ($httpCode === 0) {
            $httpCode = 200;
        }

        $this->httpCode = $httpCode;
        $this->internalMsg = $internal;
    }

    public function setModule($module = 'api')
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getInternalMessage()
    {
        if ($this->internalMsg !== '') {
            return $this->internalMsg;
        }
        return $this->getMessage();
    }
}
