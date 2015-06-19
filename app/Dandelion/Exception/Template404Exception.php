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

class Template404Exception extends \Exception
{
    public function __construct($template)
    {
        parent::__construct('Template '.$template.' not found', 404);
    }
}
