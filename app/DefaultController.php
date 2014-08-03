<?php

require_once('lib/authentication/AuthenticationManager.php');

class DefaultController extends BaseController
{
    public $user;
    public $account;

    public function __construct()
    {
    }

    public function run(HttpRequestInfo &$httpRequestInfo, HttpResponseInfo &$httpResponseInfo)
    {
        printf("<PRE>\n%s<br>\n",print_r($httpRequestInfo, true));
    }
}


