<?php
/**
 * Interface for administration module
 */
namespace Dandelion\Repos\Interfaces;

interface SessionRepo
{
    public function read($id);
    public function write($id, $data);
    public function destroy($id);
    public function gc($maxlifetime);
}
