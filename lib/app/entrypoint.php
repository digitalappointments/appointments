<?php
require_once(dirname(__FILE__)."/../../env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'app');

$httpRequestInfo = new HttpRequestInfo();
$httpRequestInfo->accept = $_SERVER["HTTP_ACCEPT"];
$httpRequestInfo->acceptEncoding = $_SERVER["HTTP_ACCEPT_ENCODING"];
$httpRequestInfo->userAgent = $_SERVER["HTTP_USER_AGENT"];
$httpRequestInfo->remoteAddress = $_SERVER["REMOTE_ADDR"];
$httpRequestInfo->requestURI = $_SERVER["REQUEST_URI"];
$httpRequestInfo->host = $_SERVER["HTTP_HOST"];
$httpRequestInfo->cookie = $_SERVER["HTTP_COOKIE"];
$httpRequestInfo->request_time = $_SERVER["REQUEST_TIME"];
if (isset($_SERVER["HTTP_X_METHOD_OVERRIDE"])) {
    $httpRequestInfo->requestMethod = $_SERVER["HTTP_X_METHOD_OVERRIDE"];
} else {
    $httpRequestInfo->requestMethod = $_SERVER["REQUEST_METHOD"];
}

list($_uri_, $_qs_) = explode("?", $_SERVER["REQUEST_URI"]);
if (!isset($_qs_)) {
    $_qs_ = "";
}

$IGNORE_TOKENS = 1;  // Ignore this many tokens as part of core url path
$tcount=0;
$URI_ELEMENT_COUNT = 0;
$URI_ELEMENTS = array();
$_uri_tokens_ = explode("/", $_uri_);
for ($i = 0; $i < count($_uri_tokens_); $i++) {
    if (strlen(trim($_uri_tokens_[$i])) > 0) {
        $tcount++;
        if ($tcount == 1) {
            $httpRequestInfo->applicationName = strtolower(trim($_uri_tokens_[$i]));
        }
        if ($tcount > $IGNORE_TOKENS) {
            $URI_ELEMENTS[$URI_ELEMENT_COUNT] = trim($_uri_tokens_[$i]);
            $URI_ELEMENT_COUNT++;
        }
    }
}

$httpRequestInfo->requestPath =  implode('/',$URI_ELEMENTS);
$httpRequestInfo->queryString = $_qs_;
unset($IGNORE_TOKENS);
unset($tcount);
unset($URI_ELEMENTS);
unset($URI_ELEMENT_COUNT);
unset($_uri_tokens_);
unset($_uri_);
unset($_qs_);
unset($i);

/*******************
* If you have an application name ... look for a controller for that application
*    Otherwise, use default Controlller
 *******************/

$httpRequestInfo->controllerClassName = "DefaultController";
$httpRequestInfo->controllerFile = "lib/app/{$httpRequestInfo->controllerClassName}.php";
if (!empty($httpRequestInfo->applicationName)) {
    $app_name = str_replace("_", " ", $httpRequestInfo->applicationName);
    $app_name = ucwords($app_name);
    $app_name = str_replace(" ", "", $app_name);

    $app_controller = "lib/app/{$httpRequestInfo->applicationName}/{$app_name}Controller.php";
    if (file_exists($app_controller)) {
        $httpRequestInfo->controllerFile = $app_controller;
        $httpRequestInfo->controllerClassName = "{$app_name}Controller";
    }
    unset($app_controller);
    unset($app_name);
}

include_once("{$httpRequestInfo->controllerFile}");
$controller = new $httpRequestInfo->controllerClassName();
$httpResponseInfo = new HttpResponseInfo();

ob_start();

//$include_file = "lib/app/{$httpRequestInfo->applicationName}/html/index.php";
//if (file_exists($include_file)) {
//    include($include_file);
//    unset($include_file);
//} else {

$postVars = $_POST;
$getVars  = $_GET;
$reqVars  = $_REQUEST;
$payload  = null;
if ( ($httpRequestInfo->requestMethod == 'POST' || $httpRequestInfo->requestMethod == 'PUT') &&
     strpos($httpRequestInfo->contentType,"json") !== false) {
    $rawData = file_get_contents('php://input');
    if (!empty($rawData)) {
        $payload = json_decode($rawData, true);
    }
}

$httpRequestInfo->postVars = $postVars;
$httpRequestInfo->getVars  = $getVars;
$httpRequestInfo->reqVars  = $reqVars;
$httpRequestInfo->payload  = $payload;
$controller->run($httpRequestInfo, $httpResponseInfo);

$extraneousData = trim(ob_get_clean());
if (strlen($extraneousData) >  0) {

}

include_once("lib/include/http_response_code.php");

if ($httpResponseInfo->status >= 200 && $httpResponseInfo->status < 300) {
    // header('HTTP/1.0 {$httpResponseInfo->status} OK');
    http_response_code($httpResponseInfo->status);
    if (strpos($httpRequestInfo->accept,"json") !== false) {
        $result = $httpResponseInfo->getResult(true);
    } else {
        $result = $httpResponseInfo->getResult();
    }
    echo $result;
    exit(0);
}

//if ($httpResponseInfo->status >= 500) {
//    header('HTTP/1.0 500 Internal Server Error');
//    exit(0);
//}
//if ($httpResponseInfo->status == 404) {
//    header('HTTP/1.0 400 Not Found');
//    exit(0);
//}

http_response_code($httpResponseInfo->status);

exit(0);
?>
