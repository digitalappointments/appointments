<?php

//----------------------------------------------------------------------------
require_once("lib/http/HttpClient.php");
HttpClient::addHeader("API_USER", md5("tjwolf"));

$remote = getenv('REMOTE');

if ($remote === '1') {
    HttpClient::$URL_PREFIX = 'http://handlemyappointments.net/appointments/rest/v10';
} else {
    HttpClient::$URL_PREFIX = 'http://localhost:8888/appointments/rest/v10';
}
// printf("\nHost URL: %s\n\n", HttpClient::$URL_PREFIX);
//----------------------------------------------------------------------------

