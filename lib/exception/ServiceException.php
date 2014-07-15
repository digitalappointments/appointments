<?php

require_once 'lib/exception/BasicException.php';

class Service {
    const Success = 200;
    const InvalidParam = 400;
    const Unauthorized = 401;
    const NoResult = 404;
    const Error = 500;
}

class ServiceException extends BasicException
{

}
