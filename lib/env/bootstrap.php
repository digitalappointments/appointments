<?php
chdir(dirname(__FILE__).'/../../');

require_once("vendor/autoload.php");
require_once("lib/env/Config.php");
require_once("lib/logger/Log.php");
require_once("lib/utils/utils.php");
require_once("lib/include/HttpRequestInfo.php");
require_once("lib/include/HttpResponseInfo.php");
require_once("lib/include/BaseController.php");
require_once("lib/utils/SystemClassLoader.php");
require_once("lib/exception/ServiceException.php");
require_once("lib/database/DBManagerFactory.php");
require_once("lib/model/Model.php");
include_once('lib/model/FieldDefinitions.php');
require_once("lib/model/BaseObject.php");
require_once("lib/model/BaseObjectServices.php");

$dbm = DBManagerFactory::getDatabaseManager();
if (empty($dbm)) {
    Log::fatal('Unable to connect to Database - terminating');
    throw new ServiceApiExceptionError('Database Configuration Error');
}
