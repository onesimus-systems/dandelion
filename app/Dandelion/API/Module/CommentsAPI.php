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

class CommentsAPI extends BaseModule
{
    public function __construct(Application $app, User $user) {
        parent::__construct($app, $user, false);
    }

    public function add()
    {
        $logObject = new Logs($this->makeRepo('Logs'));

        $logId = $this->request->postParam('logid', null);
        $commentText = $this->request->postParam('comment', '');

        if ($logObject->addComment($logId, $this->requestUser->get('id'), $commentText)) {
            return 'Comment created successfully';
        } else {
            throw new ApiException('Error creating comment', 5);
        }
    }

    public function get()
    {
        $logObject = new Logs($this->makeRepo('Logs'));

        $logId = $this->request->getParam('logid', null);
        $order = $this->request->getParam('order', 'new');

        return $logObject->getLogCommentsComment($logId, $order);
    }
}
