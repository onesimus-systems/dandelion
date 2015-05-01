<?php
/**
 * Loads the appropiate database class as defined in the user config
 */
namespace Dandelion\Storage;

class Loader
{
	public static function load($dbConfig, $debug)
	{
		$dbType = __NAMESPACE__.'\\'.ucfirst($dbConfig['type']).'Database';
		$dbType::getInstance($dbConfig, $debug);
	}
}

