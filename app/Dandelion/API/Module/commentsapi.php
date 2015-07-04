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
use Dandelion\Rights;
use Dandelion\UrlParameters;
use Dandelion\Logs;
use Dandelion\Exception\ApiException;

class CommentsAPI extends BaseModule
{
    public function __construct(Application $app, Rights $ur, UrlParameters $urlParameters) {
        parent::__construct($app, $ur, $urlParameters, false);
    }

    public function add()
    {
        $logObject = new Logs($this->makeRepo('Logs'));

        $logId = $this->up->get('logid', null);
        $commentText = $this->up->get('comment', '');

        if ($logObject->addComment($logId, USER_ID, $commentText)) {
            return 'Comment created successfully';
        } else {
            throw new ApiException('Error creating comment', 5);
        }
    }
}
