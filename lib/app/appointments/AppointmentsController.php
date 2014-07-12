<?php

class AppointmentsController extends BaseController
{
    public $user;
    public $account;


    public function __construct()
    {
       $stop = "HERE";
    }

    public function run(HttpRequestInfo &$httpRequestInfo, HttpResponseInfo &$httpResponseInfo)
    {
        printf("<PRE>\n%s<br>\n",print_r($httpRequestInfo, true));
    }
}
