<?php
require_once(dirname(__FILE__)."/../env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'api');

require_once("lib/rest/RestService.php");

// ob_start();

$service = new RestService();
$service->execute();

// $data = trim(ob_get_clean());
//echo $data . "\n";
//
//include_once("lib/include/http_response_code.php");

//if ($httpResponseInfo->status >= 200 && $httpResponseInfo->status < 300) {
//    // header('HTTP/1.0 {$httpResponseInfo->status} OK');
//    http_response_code($httpResponseInfo->status);
//    if (strpos($httpRequestInfo->accept,"json") !== false) {
//        $result = $httpResponseInfo->getResult(true);
//    } else {
//        $result = $httpResponseInfo->getResult();
//    }
//    echo $result;
//    exit(0);
//}

//if ($httpResponseInfo->status >= 500) {
//    header('HTTP/1.0 500 Internal Server Error');
//    exit(0);
//}
//if ($httpResponseInfo->status == 404) {
//    header('HTTP/1.0 400 Not Found');
//    exit(0);
//}

//http_response_code($httpResponseInfo->status);

exit(0);
?>

