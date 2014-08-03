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
    'firstName' => 'Santa',
    'lastName' => 'Claus',
    'dateEntered' => '2014-08-24 16:15:00',
    'dateModified' => '2014-08-25 10:15:00',
    'deleted' => 0,
    'email' => 'twolf@webtribune.com',
    'addressStreet' => '123 Maple',
    'addressCity' => 'Madison',
    'addressState' => 'Wisconsin',
    'addressPostalcode' => '53511',
    'addressCountry' => 'USA',
    'title' => "Vice President",
    'phoneHome' => '919-821-3220',
    'phoneMobile' => '919-844-1706',
    'phoneWork' => '',
    'phoneOther' => '',
    'phoneFax' => '919-765-8225',
    'active' => 1
);


$url = "/users/";

printf("\n\n------ CREATE User ---------------\n");
printf("URL: %s\n",HttpClient::$URL_PREFIX . $url);
$restClient = new HttpClient();
$result = $restClient->callResource('POST', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
