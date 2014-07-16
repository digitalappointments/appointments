<?php
$current_user = '1';
include_once("sugar_api_user.php");

$jsondata =<<<eod
{"id":"f10c8d83-87d9-866c-c152-502bf609c21d","name":"SPONGEBOB SQUAREPANTS","date_entered":"08\/15\/2012 03:20pm","date_modified":"08\/27\/2012 12:21pm","modified_user_id":"1","modified_by_name":"Tim Wolf","created_by":"1","created_by_name":"Tim Wolf","description":"","deleted":"0","assigned_user_id":"seed_will_id","assigned_user_name":"Will Westin","team_id":"East","team_set_id":"b76da7ef-65b4-8797-4c61-502bf6d22c24","team_name":"East","salutation":"","first_name":"SPONGEBOB","last_name":"SQUAREPANTS","full_name":"BOB SQUAREPANTS","title":"IT Developer","department":"","do_not_call":"0","phone_home":"(611) 877-1174","email":[],"phone_mobile":"(918) 379-3595","phone_work":"(991) 700-6978","phone_other":"","phone_fax":"","email1":"","email2":"","invalid_email":"","email_opt_out":"","primary_address_street":"48920 San Carlos Ave","primary_address_street_2":"","primary_address_street_3":"","primary_address_city":"Denver","primary_address_state":"CA","primary_address_postalcode":"54509","primary_address_country":"USA","alt_address_street":"","alt_address_street_2":"","alt_address_street_3":"","alt_address_city":"","alt_address_state":"","alt_address_postalcode":"","alt_address_country":"","assistant":"","assistant_phone":"","converted":"0","refered_by":"","lead_source":"Employee","lead_source_description":"","status":"Dead","status_description":"","reports_to_id":"","report_to_name":"","account_name":"Lexington Shores Corp","account_description":"","contact_id":"","account_id":"","opportunity_id":"","opportunity_name":"","opportunity_amount":"","campaign_id":"","campaign_name":"","c_accept_status_fields":"","m_accept_status_fields":"","accept_status_id":"","accept_status_name":"","webtolead_email1":"","webtolead_email2":"","webtolead_email_opt_out":"","webtolead_invalid_email":"","birthdate":"","portal_name":"","portal_app":"","website":"","preferred_language":"en_us"}
eod;

$data = json_decode($jsondata,true);
//print_r($data);

//echo "..sleeping\n";
//sleep(5);
$response = callResource("/Leads/".$data["id"], 'DELETE', $data);

print_r($response);

$code = $response["code"];
printf("HTTP Status Code: $code\n");
if ($code == 200) {
	print_r($response["data"]);
} else {
	print_r($response["response_headers"]);
}
?>