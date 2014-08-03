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

    public $args=array();
    public $payload = '';
    public $cookie = '';

    public $request_time;

    public function setArgs() {
        parse_str($this->queryString, $this->args);
        return $this->getArgs();
    }

    public function getArgs() {
        return $this->args;
    }
}
