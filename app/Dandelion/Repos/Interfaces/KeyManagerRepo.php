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

interface KeyManagerRepo
{
    public function getKeyForUser($uid);
    public function saveKeyForUser($uid, $key);
    public function revoke($uid);
}
