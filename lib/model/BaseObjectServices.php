<?php
/**
 * @package    Publisher
 * @subpackage Service
 * @version    $Id: Service.php 1307 2007-12-10 15:02:43Z mfrench $
 */

/**
 * @package    Publisher
 * @subpackage Service
 */
abstract class BaseObjectServices
{
    /**
     * @var int
     */
    const Success = 200;

    /**
     * @var int
     */
    const InvalidParam = 400;

    /**
     * @var int
     */
    const Unauthorized = 401;

    /**
     * @var int
     */
    const NoResult = 404;

    /**
     * @var int
     */
    const Error = 500;

    /**
     * Array of Service objects being instantiated.  Useful for unit-testing.
     *
     * @ignore
     * @var array
     */
    private static $aServiceInstances = array();

    /**
     * @ignore
     */
    public function __construct()
    {
    }


    //////////////////////////////////////////////////////////////////////////
    // Pseudo Protected methods /////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves the service by the specified name.
     *
     * @ignore
     * @param String $sServiceName
     * @return Service
     */
    public static function _getService($sServiceName)
    {
        if (!(isset(self::$aServiceInstances[$sServiceName]))) {
            $sServiceClass = "{$sServiceName}Service";
            $oService = new $sServiceClass();
            if (!($oService instanceof Service)) {
                throw new Exception("'{$sServiceName}Service' is not an instance of 'Service'");
            }
            self::_setService($sServiceName, $oService);
        }

        return self::$aServiceInstances[$sServiceName];
    }

    /**
     * Empties out the registered services.
     *
     * @ignore
     */
    public static function _resetServices()
    {
        self::$aServiceInstances = array();
    }

    /**
     * Specifies a new service object for the service with the specified name.
     *
     * @ignore
     * @param String $sServiceName
     * @param Service $oService
     * @return Service
     */
    public static function _setService($sServiceName, Service $oService)
    {
        $sClassName = "{$sServiceName}Service";
        if (!($oService instanceof $sClassName)) {
            throw new Exception("Supplied service name '{$sClassName}' does not match supplied service instance type '".get_class($oService)."'");
        }
        self::$aServiceInstances[$sServiceName] = $oService;
    }

    //////////////////////////////////////////////////////////////////////////
    // Protected methods ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     * Logs some debugging info. Takes as many params as you want, print_r's them before logging.
     */
    protected function debug()
    {
        $aArgs = func_get_args();
        $sOut = "";
        foreach ($aArgs as $sMsg) {
            if (is_string($sMsg)) {
                $sOut .= $sMsg;
            } else {
                $sOut .= print_r($sMsg, true);
            }
        }
        LOG::$l->debug(get_class($this) . ": {$sOut}\n");
    }
}
