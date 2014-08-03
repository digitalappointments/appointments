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
    "id", "firstName", "lastName", "dateModified", "addressCity", "deleted"
);
$fieldOrder = array(
    "lastName", "id",
);
$fieldDir = array(
    "desc",
);

$fieldSelector      = "fields="    . implode(",", $fields);
$fieldOrderSelector = "order_by="  . implode(",", $fieldOrder);
$fieldDirSelector   = "order_dir=" . implode(",", $fieldDir);
$maxNum             = "max_num=100";
$options            = "__deleted=true";   // all must be double_underscored variables

//    order_by=id,name&order_dir=DESC&max_num=2&fields=name,dateModified,addressCity'

$args = array(
    $fieldSelector,
    $fieldOrderSelector,
    $fieldDirSelector,
    $maxNum,
    // $options,
);

$url = "/users";
if (!empty($args)) {
    $url .= "?" . implode("&", $args);
}

printf("\n\n------ LIST Users ---------------\n");
printf("URL: %s\n",HttpClient::$URL_PREFIX . $url);
$restClient = new HttpClient();
$result = $restClient->callResource('GET', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
