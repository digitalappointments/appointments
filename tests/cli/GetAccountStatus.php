<?php   
include("../../lib/http/HttpClient.php");
$restClient = new HttpClient();   

$method = 'GET';
$uri = '/account/status';
$result = $restClient->callResource($method, $uri);

print_r($result);
echo "\n";
exit;