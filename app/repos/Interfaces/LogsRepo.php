<?php
/**
 * Interface for administration module
 */
namespace Dandelion\Repos\Interfaces;

interface LogsRepo
{
    public function numOfLogs();
    public function getLogInfo($lid);
    public function getLogList($offset, $limit);
    public function addLog($uid, $title, $body, $cat, $date, $time);
    public function updateLog($lid, $title, $body, $cat);
    public function getLogsByFilter($filter);
    public function getLogsBySearch($kw, $date);
}
