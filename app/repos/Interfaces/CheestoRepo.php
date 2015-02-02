<?php
/**
 * Interface for the Cheesto module
 */
namespace Dandelion\Repos\Interfaces;

interface CheestoRepo
{
    public function getAllStatuses();
    public function getUserStatus($uid);
    public function updateStatus($uid, $status, $message, $return, $date);
}
