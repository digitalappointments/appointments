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

$data = array (
    'firstName' => 'Sandy',
    'addressStreet' => '1823 Henderson Ave',
    'addressCity' => 'Milwaukee',
);


$url = "/users/c6700dfc-623f-6efc-c12c-53c7468bc8f7";

printf("\n\n------ UPDATE User ---------------\n");
$restClient = new HttpClient();
$result = $restClient->callResource('PUT', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
