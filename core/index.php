<?php
$trace = false;

chdir(dirname(__FILE__).'/../');
include_once("lib/include/http_response_code.php");

parse_str($_SERVER['QUERY_STRING'], $args);

if ($trace) {
    printf("%s\n", __FILE__);
    printf("<PRE>\nargs:\n");
    print_r($args);
}

$requestURL = $_SERVER['REDIRECT_URL'];
$core = "core/";
$off = strpos($requestURL, ('/' . $core));
if ($off === FALSE) {
    $requestPath = '';
} else {
    $requestPath = substr($requestURL,$off + 1 + strlen($core));
}

if ($trace) {
    printf ("RequestPath: '%s'\n",$requestPath);
}
if (!empty($requestPath) && file_exists($core . $requestPath)) {
    $file = $core . $requestPath;
    $path_parts = pathinfo($file);
    $ext = $path_parts['extension'];
    if ($ext == 'php') {
        include_once($file);
        // http_response_code(200);
        exit;
    }
}
//header("HTTP/1.0 404 Not Found");
http_response_code(404);
exit;



