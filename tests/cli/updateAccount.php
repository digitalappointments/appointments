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
    'name' => 'Her Fleece as White as Snow',
    'addressStreet' => '1823 Henderson Ave',
    'addressCity' => 'Milwaukee',
    'altPhone' => '919-821-3220',
);


$url = "/accounts/a90009cf-8f08-2832-df16-53c723d319ba";

printf("\n\n------ UPDATE Account ---------------\n");
$restClient = new HttpClient();
$result = $restClient->callResource('PUT', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
