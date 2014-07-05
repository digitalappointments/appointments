<?php
// Insert the path where you unpacked log4php
require_once "vendor/autoload.php";

// Tell log4php to use our configuration file.
Logger::configure("lib/logger/config.php");


/**
 * This is a classic usage pattern: one logger object per class.
 */
class Log
{
    /** Holds the Logger. **/
    private static $log;

    private static function getLogger()
    {
        if (!self::$log) {
            self::$log = Logger::getLogger(__CLASS__);
        }
        return self::$log;
    }

    /** Logger can be used from any member method. */
    public static function trace($message)
    {
        self::getLogger()->trace($message);
    }

    public static function debug($message)
    {
        self::getLogger()->debug($message);
    }

    public static function info($message)
    {
        self::getLogger()->info($message);
    }

    public static function warn($message)
    {
        self::getLogger()->warn($message);
    }

    public static function error($message)
    {
        self::getLogger()->error($message);
    }

    public static function fatal($message)
    {
        self::getLogger()->fatal($message);
    }
}

