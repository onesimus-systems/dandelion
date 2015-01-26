<?php
/**
 * Error logging for PHP errors
 */
namespace Dandelion;

class Logging
{
    private static $path;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     *  Register the logging system with Dandelion
     */
    public static function register($path)
    {
        self::$path = $path;
        set_error_handler('\Dandelion\Logging::errorHandler');
        register_shutdown_function('\Dandelion\Logging::shutdownHandler');
        error_reporting(E_ALL);
        ini_set('log_errors', true);

        if (true) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } else {
            ini_set('display_errors', false);
            ini_set('display_startup_errors', false);
        }
    }

    /**
     *  Normal error handler
     */
    public static function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
    {
        $errortype = array(
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

        $error = date("Y-m-d H:i:s") . ' | ' . $errortype[$error_level] . ' | Message: ' . $error_message . ' | File: ' . $error_file . ' | Ln: ' . $error_line;
        switch ($error_level) {
            case E_ERROR:
                // no break
            case E_CORE_ERROR:
                // no break
            case E_COMPILE_ERROR:
                // no break
            case E_PARSE:
                self::logToFile($error, "fatal");
                exit(1);
                break;
            case E_USER_ERROR:
                // no break
            case E_RECOVERABLE_ERROR:
                self::logToFile($error, "error");
                break;
            case E_WARNING:
                // no break
            case E_CORE_WARNING:
                // no break
            case E_COMPILE_WARNING:
                // no break
            case E_USER_WARNING:
                self::logToFile($error, "warn");
                break;
            case E_NOTICE:
                // no break
            case E_USER_NOTICE:
                self::logToFile($error, "info");
                break;
            case E_STRICT:
                self::logToFile($error, "debug");
                break;
            default:
                self::logToFile($error, "warn");
        }
        return;
    }

    /**
     *  Handler for harder shutdown errors
     */
    public static function shutdownHandler()
    {
        session_write_close();
        $lasterror = error_get_last();
        switch ($lasterror['type']) {
            case E_ERROR:
                // no break
            case E_CORE_ERROR:
                // no break
            case E_COMPILE_ERROR:
                // no break
            case E_USER_ERROR:
                // no break
            case E_RECOVERABLE_ERROR:
                // no break
            case E_CORE_WARNING:
                // no break
            case E_COMPILE_WARNING:
                // no break
            case E_PARSE:
                $error = date("Y-m-d H:i:s") . " | [SHUTDOWN] Level: " . $lasterror['type'] . " | Message: " . $lasterror['message'] . " | File: " . $lasterror['file'] . " | Ln: " . $lasterror['line'];
                self::logToFile($error, "fatal");
        }
        exit(1);
    }

    /**
     *  Log all errors to file with the file name of their error level
     */
    private static function logToFile($error, $errlvl)
    {
        if (!is_dir(self::$path)) {
            mkdir(self::$path, 0740);
        }
        $logpath = self::$path.'/'.$errlvl.'.log';
        return file_put_contents($logpath, $error.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
