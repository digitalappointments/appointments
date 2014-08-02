<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

if ($argc > 1) {
    $id = $argv[1];
} else {
    printf("Required Argument: id\n\n");
    exit;
}

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

$options            = "__deleted=true";   // all must be double_underscored variables

$args = array(
    // $options,
);

$url = "/accounts/{$id}";
if (!empty($args)) {
    $url .= "?" . implode("&", $args);
}

printf("\n\n------ UPDATE Account ---------------\n");
printf("URL: %s\n",$url);
$restClient = new HttpClient();
$result = $restClient->callResource('PUT', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
