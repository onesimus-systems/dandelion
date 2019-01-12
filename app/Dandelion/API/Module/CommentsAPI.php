<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\Application;
use Dandelion\User;
use Dandelion\Logs;
use Dandelion\Exception\ApiException;
use Dandelion\API\ApiCommander;

class CommentsAPI extends BaseModule
{
    protected $makeRepo = false;

    public function add($params)
    {
        $logObject = new Logs($this->makeRepo('Logs'));

        if ($logObject->addComment($params->logid, $this->requestUser->get('id'), $params->comment)) {
            return 'Comment created successfully';
        } else {
            throw new ApiException('Error creating comment',ApiCommander::API_GENERAL_ERROR);
        }
    }

    public function get($params)
    {
        $logObject = new Logs($this->makeRepo('Logs'));
        return $logObject->getLogComments($params->logid, $params->order);
    }
}
