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
    "id", "firstName", "lastName", "dateModified", "addressCity"
);
$fieldSelector      = "fields="    . implode(",", $fields);

$args = array(
    $fieldSelector,
);

$qs = "?" . implode("&", $args);
// echo $qs . "\n";

$id = 'c6700dfc-623f-6efc-c12c-53c7468bc8f7';
$url = "/users/{$id}" . $qs;

printf("\n\n------ GET User ---------------  ID: %s\n", $id);
$restClient = new HttpClient();
$result = $restClient->callResource('GET', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
