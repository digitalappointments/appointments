<?php
chdir(dirname(__FILE__).'/../../');

require_once("vendor/autoload.php");
require_once("lib/env/Config.php");
require_once("lib/logger/Log.php");
require_once("lib/utils/utils.php");
require_once("lib/utils/SystemClassLoader.php");
require_once("lib/controller/Controller.php");
require_once("lib/exception/ServiceException.php");
require_once("lib/database/DBManagerFactory.php");

define("TRACE", false);

$REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
$QUERY_STRING = $_SERVER["QUERY_STRING"];
$REQUEST_URI = $_SERVER["REQUEST_URI"];
$HTTP_HOST = $_SERVER["HTTP_HOST"];

if (isset($_SERVER["HTTP_X_METHOD_OVERRIDE"])) {
    $REQUEST_METHOD = $_SERVER["HTTP_X_METHOD_OVERRIDE"];
    $_SERVER["REQUEST_METHOD"] = $REQUEST_METHOD;
} else {
    $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
}

$CURRENT_DIRECTORY = getcwd();
$THIS_FILE_DIRECTORY = dirname(__FILE__);

list($_uri_, $_qs_) = explode("?", $REQUEST_URI);
if (!isset($_qs_)) {
    $_qs_ = "";
}

if (TRACE) {
    printf("<PRE>\n");
    printf("<br>\n== REWRITE ==== REQUEST RECEIVED : %s ===============<br>\n", $REQUEST_URI);
}

$IGNORE_TOKENS = 1;  // Ignore this many tokens as part of core url path

$tcount=0;
$PAREF = "";
$URI_ELEMENT_COUNT = 0;
$_uri_tokens_ = explode("/", $_uri_);
for ($i = 0; $i < count($_uri_tokens_); $i++) {
    if (strlen(trim($_uri_tokens_[$i])) > 0) {
        $tcount++;
        if ($tcount > $IGNORE_TOKENS) {
            $URI_ELEMENTS[$URI_ELEMENT_COUNT] = trim($_uri_tokens_[$i]);
            if (TRACE) {
                printf("<br> [%d] ... %s<br>\n", $URI_ELEMENT_COUNT, $URI_ELEMENTS[$URI_ELEMENT_COUNT]);
            }
            $URI_ELEMENT_COUNT++;
        }
    }
}

$include_file = "lib/app/home.php";
if ($URI_ELEMENT_COUNT == 1) {
    $token = strtolower($URI_ELEMENTS[0]);
    if (file_exists("lib/app/" . $token . ".php")) {
        $include_file = "lib/app/" . $token . ".php";
    }
    unset($token);
}

if (TRACE) {
    printf("FILE-DIRECTORY    = %s\n", $THIS_FILE_DIRECTORY);
    printf("REMOTE_ADDR       = %s\n", $REMOTE_ADDR);
    printf("QUERY_STRING      = %s\n", $QUERY_STRING);
    printf("REQUEST_URI       = %s\n", $REQUEST_URI);
    printf("HTTP_HOST         = %s\n", $HTTP_HOST);
    printf("REQUEST_METHOD    = %s\n", $REQUEST_METHOD);
    printf("INCLUDE_FILE      = %s\n", $include_file);

    printf("URI_ELEMENT_COUNT = %d\n", $URI_ELEMENT_COUNT);
    for ($i = 0; $i < $URI_ELEMENT_COUNT; $i++) {
        printf("... [%d]  %s\n", $i, $URI_ELEMENTS[$i]);
    }

    unset($i);
    unset($_uri_);
    unset($_qs_);
    unset($_uri_tokens_);
    unset($tcount);
    unset($PAREF);
    unset($IGNORE_TOKENS);
    unset($URI_ELEMENT_COUNT);

    unset($THIS_FILE_DIRECTORY);
    unset($REMOTE_ADDR);
    unset($QUERY_STRING);
    unset($REQUEST_URI);
    unset($HTTP_HOST);
    unset($REQUEST_METHOD);
    unset($include_file);

    exit;
}

unset($i);
unset($_uri_);
unset($_qs_);
unset($_uri_tokens_);
unset($tcount);
unset($PAREF);
unset($IGNORE_TOKENS);
unset($URI_ELEMENT_COUNT);
unset($THIS_FILE_DIRECTORY);
unset($REMOTE_ADDR);
unset($QUERY_STRING);
unset($REQUEST_URI);
unset($HTTP_HOST);
unset($REQUEST_METHOD);

include($include_file);

unset($include_file);
exit;
?>
