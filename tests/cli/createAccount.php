<?php
// $current_user = '1';
include_once("sugar_api.php");

$GLOBALS['config']['apiUrl'] = 'http://localhost:8888/Mango/toffee/ent/sugarcrm/rest/v10';
$GLOBALS['config']['username'] = 'sally';
$GLOBALS['config']['password'] = 'sally';

$jsondata =<<<eod
{"deleted":"0","do_not_call":"0","converted":"0","preferred_language":"en_us","assigned_user_id":"seed_sally_id","team_name":[{"id":1,"display_name":"Global","name":"Global","name_2":"","primary":true}],"last_name":"Qwerty","full_name":"Qwerty"}
eod;

$data = json_decode($jsondata,true);
print_r($data);

//echo "..sleeping\n";
//sleep(5);

// $response = callResource("/Contacts/link/leads?viewed=1", 'POST', $data);
$response = callResource("/Leads?viewed=1", 'POST', $data);

//  print_r($response);

$code = $response["code"];
printf("HTTP Status Code: $code\n");
if ($code == 200) {
	print_r($response["data"]);
} else {
	print_r($response["response_headers"]);
}
?>