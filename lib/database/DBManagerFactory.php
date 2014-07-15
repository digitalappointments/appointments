<?php
require_once('lib/database/DatabaseException.php');
require_once('lib/database/DBManager.php');
require_once('lib/database/D.php');

class DBManagerFactory
{
    /**
     * @var Database Manager
     */
    protected static $dbm;

    /**
     * Get Database Manager configured for this instance
     * Configured Database Manager is loaded if needed
     * @param  bool $reset
     * @return $dbm Database Manager
     */
    public static function getDatabaseManager($reset = false)
    {
        if ($reset) {
            self::reset();
        }
        if (empty(self::$dbm)) {
            $dbConfig = Config::getDatabaseConfiguration();
            $dbmClassName = $dbConfig['dbManagerClassName'];
            $dbmClassPath = $dbConfig['dbManagerClassPath'];
            $dbmFileName  = "{$dbmClassPath}/{$dbmClassName}.php";
            if (file_exists($dbmFileName)) {
                include_once($dbmFileName);
            }
            self::$dbm = SystemClassLoader::getInstance($dbmClassName);
            self::$dbm->dbConfig = $dbConfig;
            self::$dbm->connect();
        }
        D::$dbm = self::$dbm;;
        return self::$dbm;
    }

    /**
     * Reset Database Manager and Connection Handle
     */
    protected static function reset()
    {
        self::$dbm = null;
    }
}
