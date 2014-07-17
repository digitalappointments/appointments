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


//
////~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  CREATE FROM ~~~~~~~~~~~~
//$url = "/accounts/32749ec7-1361-5bf8-2fc1-53c4c2e524f2";
//$restClient = new HttpClient();
//$result = $restClient->callResource('GET', $url, $data);
//if ($result['code'] != '200') {
//    print_r($result);
//    echo "\n";
//    exit;
//}
//// print_r($result['data']);
////~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  CREATE FROM ~~~~~~~~~~~~
//
//$data = $result['data'];
//unset($data['id']);
//
//var_export($data);
//exit;
//

$data = array (
    'name' => 'Mary Had A little Lamb',
    'dateEntered' => '2014-08-24 16:15:00',
    'dateModified' => '2014-08-25 10:15:00',
    'deleted' => 0,
    'industry' => '',
    'addressStreet' => '',
    'addressCity' => 'Madison',
    'addressState' => 'Wisconsin',
    'addressPostalcode' => '53511',
    'addressCountry' => '',
    'officePhone' => '',
    'altPhone' => '',
    'website' => '',
    'active' => 0,
    'trial' => 0,
);


$url = "/accounts/";

printf("\n\n------ CREATE Account ---------------\n");
$restClient = new HttpClient();
$result = $restClient->callResource('POST', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
