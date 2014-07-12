<?php

class HttpRequestInfo
{
    public $accept = '';
    public $acceptEncoding = '';

    public $userAgent = '';
    public $host = '';
    public $remoteAddress = '';

    public $applicationName = '';
    public $controllerClassName = '';
    public $controllerFile = '';
    public $requestMethod = '';
    public $requestURI = '';
    public $requestPath = '';
    public $queryString = '';

    public $cookie;
    public $payload;
    public $request_time;
}
