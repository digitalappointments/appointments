<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

include_once('tests/cli/HttpConfig.php'); 
HttpClient::$URL_PREFIX = 'http://localhost:8888/tgm';

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
);

// http://localhost:8888/tgm/abc/def/signup.php?abc=25&def=456&xyz=77  

$url = "/abc/def/signup.php?abc=25&def=456&xyz=77\n";

printf("\n\n------ POST APP ---------------\n");
printf("URL: %s\n",HttpClient::$URL_PREFIX . $url);
$restClient = new HttpClient();
$result = $restClient->callResource('POST', $url, $data);
print_r($result);

if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
