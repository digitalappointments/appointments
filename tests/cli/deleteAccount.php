<?php
include_once("sugar_api.php");
$GLOBALS['config']['apiUrl'] = 'http://localhost:8888/Mango/toffee/ent/sugarcrm/rest/v10';
 
// http://localhost:8888/Mango/toffee/ent/sugarcrm/rest/v10/EmailCommunications/4c6f978f-ad6e-876f-328d-535c3a227b77
$response = callResource("/EmailCommunications/4c6f978f-ad6e-876f-328d-535c3a227b77", 'DELETE');

print_r($response);

$code = $response["code"];
printf("HTTP Status Code: $code\n");
if ($code == 200) {
	print_r($response["data"]);
} else {
	print_r($response["response_headers"]);
}
?>