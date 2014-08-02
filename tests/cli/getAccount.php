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


$fields = array(
    "id", "name", "dateModified", "addressCity", "deleted"
);
$fieldSelector      = "fields="    . implode(",", $fields);
$options            = "__deleted=true";   // all must be double_underscored variables

$args = array(
    $fieldSelector,
    // $options,
);

$url = "/accounts/{$id}";
if (!empty($args)) {
    $url .= "?" . implode("&", $args);
}

printf("\n\n------ GET Account ---------------  ID: %s\n", $id);
printf("URL: %s\n",$url);
$restClient = new HttpClient();
$result = $restClient->callResource('GET', $url, $data);
if ($result['code'] != '200') {
    print_r($result);
    echo "\n";
    exit;
}
print_r($result['data']);
