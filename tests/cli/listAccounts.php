<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

include_once('tests/cli/HttpConfig.php');
//----------------------------------------------------------------------------
//require_once("lib/http/HttpClient.php");
//HttpClient::addHeader("API_USER", md5("tjwolf"));
//
//HttpClient::$URL_PREFIX = 'http://localhost:8888/appointments/rest/v10';
//// HttpClient::$URL_PREFIX = 'http://handlemyappointments.net/appointments/rest/v10';
//----------------------------------------------------------------------------


$fields = array(
    "name", "dateModified", "addressCity"
);
$fieldOrder = array(
    "name", "id",
);
$fieldDir = array(
    "desc",
);

$fieldSelector      = "fields="    . implode(",", $fields);
$fieldOrderSelector = "order_by="  . implode(",", $fieldOrder);
$fieldDirSelector   = "order_dir=" . implode(",", $fieldDir);
$maxNum             = "max_num=3";

//    order_by=id,name&order_dir=DESC&max_num=2&fields=name,dateModified,addressCity'

$args = array(
    $fieldSelector,
    $fieldOrderSelector,
    $fieldDirSelector,
    $maxNum
);

$qs = "?" . implode("&", $args);
// echo $qs . "\n";

$url = "/accounts" . $qs;

printf("\n\n------ LIST Accounts ---------------\n");
$restClient = new HttpClient();
$result = $restClient->callResource('GET', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
