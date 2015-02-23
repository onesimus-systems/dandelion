<?php
/**
 * Interface for an API key manager repository
 */
namespace Dandelion\Repos\Interfaces;

interface KeyManagerRepo
{
    public function getKeyForUser($uid);
    public function saveKeyForUser($uid, $key);
    public function revoke($uid);
}
