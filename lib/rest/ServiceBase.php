<?php
abstract class ServiceBase {
    public $user;
    public $platform = 'base';
    public $action = 'view';

    abstract public function execute();
    abstract protected function handleException(Exception $exception);

    protected function loadServiceDictionary($dictionaryName) {
    }

    protected function loadApiClass($route) {
        $apiClassName = $route['className'];
        $apiClass =  SystemClassLoader::getInstance($apiClassName);
        return $apiClass;
    }

    /**
     * This function loads various items needed to setup the user's environment
     */
    protected function loadUserEnvironment()
    {

    }

    /**
     * This function loads various items when the user is not logged in
     */
    protected function loadGuestEnvironment()
    {

    }

    /**
     * Set a response header
     * @param string $header
     * @param string $info
     * @return bool
     */
    public function setHeader($header, $info)
    {
        // do nothing in base class
        return $this;
    }

    /**
     * Generate suitable ETag for content
     *
     * This function generates the necessary cache headers for using ETags with dynamic content. You
     * simply have to generate the ETag, pass it in, and the function handles the rest.
     *
     * @param string $etag ETag to use for this content.
     * @return bool Did we have a match?
     */
    public function generateETagHeader()
    {
        // do nothing in base class
        return false;
    }

    /**
     * Set response to be read from file
     */
    public function fileResponse($filename)
    {
        return false;
    }

    /**
     * Release session data
     * Keeps $_SESSION but it's no longer preserved after the end of the request
     */
    protected function releaseSession()
    {
    }

    /**
     * Handle the situation where the API needs login
     * @param Exception $e Exception that caused the login problem, if any
     * @throws ServiceApiExceptionNeedLogin
     */
    public function needLogin(Exception $e = null)
    {
    }

    /**
     * Capture PHP error output and handle it
     *
     * @param string $errorType The error type to hand down through the exception (default: 'php_error')
     * @throw ServiceApiExceptionError
     */
    public function handleErrorOutput($errorType = 'php_error')
    {
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            // Looks like something errored out first
            $errorOutput = ob_get_clean();
            Log::error("A PHP error occurred:\n".$errorOutput);
            $e = new ServiceApiExceptionError();
            $e->errorLabel = $errorType;
            throw $e;
        }
    }
}
