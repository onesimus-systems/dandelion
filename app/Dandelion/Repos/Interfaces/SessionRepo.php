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

interface SessionRepo
{
    public function read($id);
    public function write($id, $data);
    public function destroy($id);
    public function gc($maxlifetime);
}
