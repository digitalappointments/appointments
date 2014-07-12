<?php
chdir(dirname(__FILE__).'/../../');

ob_start();

require_once("vendor/autoload.php");
require_once("lib/env/Config.php");
require_once("lib/logger/Log.php");
require_once("lib/utils/utils.php");
require_once("lib/utils/SystemClassLoader.php");
require_once("lib/controller/BaseController.php");
require_once("lib/exception/ServiceException.php");
require_once("lib/exception/ServiceApiException.php");
require_once("lib/database/DBManagerFactory.php");

define('ENTRY_POINT_TYPE', 'api');

require_once("lib/rest/RestService.php");

$service = new RestService();
$service->execute();
