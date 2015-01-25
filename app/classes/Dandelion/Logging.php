<?php
/**
  * Log PHP errors
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  * The full GPLv3 license is available in LICENSE.md in the root.
  *
  * @author Lee Keitel
  * @date Jan 2015
***/
namespace Dandelion;

class Logging
{
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function register() {
        set_error_handler('\Dandelion\Logging::errorHandler');
        register_shutdown_function('\Dandelion\Logging::shutdownHandler');

        error_reporting(E_ALL);
        ini_set('log_errors', true);

        if (DEBUG_ENABLED) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } else {
            ini_set('display_errors', false);
            ini_set('display_startup_errors', false);
        }
    }

    public static function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
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

        $error = date("Y-m-d H:i:s") . ' | ' . $errortype[$error_level] . ' | Message: ' . $error_message . ' | File: ' . $error_file . ' | Ln: ' . $error_line;
        switch ($error_level) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
                self::logToFile($error, "fatal");
                exit(1);
                break;
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                self::logToFile($error, "error");
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                self::logToFile($error, "warn");
                break;
            case E_NOTICE:
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

    public static function shutdownHandler()
    {
        session_write_close();
        $lasterror = error_get_last();
        switch ($lasterror['type']) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:
                $error = date("Y-m-d H:i:s") . " | [SHUTDOWN] Level: " . $lasterror['type'] . " | Message: " . $lasterror['message'] . " | File: " . $lasterror['file'] . " | Ln: " . $lasterror['line'];
                self::logToFile($error, "fatal");
        }
        exit(1);
    }

    private static function logToFile($error, $errlvl)
    {
        if (!is_dir(ROOT.'/logs')) {
            mkdir(ROOT.'/logs', 0740);
        }
        $logpath = ROOT.'/logs/'.$errlvl.'.log';
        return file_put_contents($logpath, $error.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
