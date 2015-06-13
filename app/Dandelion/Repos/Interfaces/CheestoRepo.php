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

interface CheestoRepo
{
    public function getAllStatuses();
    public function getUserStatus($uid);
    public function updateStatus($uid, $status, $message, $return, $date);
}
