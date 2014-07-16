<?php
// $current_user = '1';
include_once("sugar_api.php");
$GLOBALS['config']['apiUrl'] = 'http://localhost:8888/Mango/toffee/ent/sugarcrm/rest/v10';

$response = callResource("/Leads/51960a74-f3a8-16e5-177e-52716e102f58", 'GET', null);

$code = $response["code"];
printf("\nHTTP Status Code: $code\n");
if ($code == 200) {
    print_r($response['data']);
} else {
	print_r($response["response_headers"]);
}