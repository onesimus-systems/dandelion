<?php
/**
  * This script handles all logging functions
  * for Dandelion.
  *
  * @author jdias, edited by Lee Keitel for Dandelion
  * @date September 2011
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/
function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
{
	$errortype = array (
			E_ERROR              => 'Error',
			E_WARNING            => 'Warning',
			E_PARSE              => 'Parsing Error',
			E_NOTICE             => 'Notice',
			E_CORE_ERROR         => 'Core Error',
			E_CORE_WARNING       => 'Core Warning',
			E_COMPILE_ERROR      => 'Compile Error',
			E_COMPILE_WARNING    => 'Compile Warning',
			E_USER_ERROR         => 'User Error',
			E_USER_WARNING       => 'User Warning',
			E_USER_NOTICE        => 'User Notice',
			E_STRICT             => 'Runtime Notice',
			E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	);
	
	$error = $errortype[$error_level] . ' | Message: ' . $error_message . ' | File: ' . $error_file . ' | Ln: ' . $error_line;
	switch ($error_level) {
	    case E_ERROR:
	    case E_CORE_ERROR:
	    case E_COMPILE_ERROR:
	    case E_PARSE:
	        logToFile($error, "fatal");
	        break;
	    case E_USER_ERROR:
	    case E_RECOVERABLE_ERROR:
	        logToFile($error, "error");
	        break;
	    case E_WARNING:
	    case E_CORE_WARNING:
	    case E_COMPILE_WARNING:
	    case E_USER_WARNING:
	        logToFile($error, "warn");
	        break;
	    case E_NOTICE:
	    case E_USER_NOTICE:
	        logToFile($error, "info");
	        break;
	    case E_STRICT:
	        logToFile($error, "debug");
	        break;
	    default:
	        logToFile($error, "warn");
	}
}

function shutdownHandler()
{
	$lasterror = error_get_last();
	switch ($lasterror['type'])
	{
	    case E_ERROR:
	    case E_CORE_ERROR:
	    case E_COMPILE_ERROR:
	    case E_USER_ERROR:
	    case E_RECOVERABLE_ERROR:
	    case E_CORE_WARNING:
	    case E_COMPILE_WARNING:
	    case E_PARSE:
	        $error = "[SHUTDOWN] Level: " . $lasterror['type'] . " | Message: " . $lasterror['message'] . " | File: " . $lasterror['file'] . " | Ln: " . $lasterror['line'];
	        logToFile($error, "fatal");
	}
}

function logToFile($error, $errlvl)
{
	$logpath = ROOT.'/logs/'.$errlvl.'.log';
	file_put_contents($logpath, $error.PHP_EOL, FILE_APPEND | LOCK_EX);
}

set_error_handler("errorHandler");
register_shutdown_function("shutdownHandler");