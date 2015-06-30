<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos\Interfaces;

interface LogsRepo
{
    public function numOfLogs();
    public function getLogInfo($lid);
    public function getLogList($offset, $limit);
    public function addLog($uid, $title, $body, $cat, $date, $time);
    public function updateLog($lid, $title, $body, $cat);
    public function getLogsBySearch($query, $limit, $offset);
}
