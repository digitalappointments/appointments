<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

//----------------------------------------------------------------------------
require_once("lib/http/HttpClient.php");
HttpClient::addHeader("API_USER", md5("tjwolf"));

HttpClient::$URL_PREFIX = 'http://localhost:8888/appointments/rest/v10';
// HttpClient::$URL_PREFIX = 'http://handlemyappointments.net/appointments/rest/v10';
//----------------------------------------------------------------------------


$fields = array(
    "name", "dateModified", "addressCity"
);
$fieldSelector      = "fields="    . implode(",", $fields);

$args = array(
    $fieldSelector,
);

$qs = "?" . implode("&", $args);
// echo $qs . "\n";

$url = "/accounts/1e092baf-d07f-eb65-101c-53c4c20bd795" . $qs;

printf("\n\n------ GET Account ---------------\n");
$restClient = new HttpClient();
$result = $restClient->callResource('GET', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
