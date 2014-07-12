<?php

require_once('lib/authentication/AuthenticationManager.php');

class BaseController
{
    public $user;
    public $account;

    public function __construct()
    {
    }

    public function run(HttpRequestInfo &$httpRequestInfo, HttpResponseInfo &$httpResponseInfo)
    {
        print_r($httpRequestInfo);
    }

    /**
     * Handles exception responses
     *
     * @param Exception $exception
     */
    protected function handleException(Exception $exception)
    {
        // $httpError = $exception->getHttpCode();
        // $errorLabel = $exception->getErrorLabel();
        $message = $exception->getMessage();

        Log::error("Exception Thrown: {$message}");
    }
}

