<?php

require_once('lib/authentication/AuthenticationManager.php');

class Controller
{
    public $user;
    public $account;

    public function __construct()
    {
    }

    /**
     * This function executes the current request and outputs the response directly.
     */
    public function execute()
    {
       // ob_start();

        try {

            $dbm = DBManagerFactory::getDatabaseManager();
            if (empty($dbm)) {
                Log::fatal('Unable to connect to Database - terminating');
                throw new BasicException('Database Configuration Error');
            }

        } catch ( Exception $e ) {
            $this->handleException($e);
        }
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

