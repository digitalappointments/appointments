<?php
/**
* This is the Map of Endpoints to ClassName(s)
* implementing those Endpoints
*/
require_once('lib/rest/RestEndpoints.php');

require_once('lib/rest/ServiceBase.php');
require_once("lib/rest/ServiceApi.php");
require_once('lib/rest/RestResponse.php');
require_once('lib/rest/RestRequest.php');
require_once('lib/authentication/AuthenticationManager.php');

require_once("lib/rest/RestClient.php");
require_once("lib/exception/ServiceApiException.php");

    /** @noinspection PhpInconsistentReturnPointsInspection */
class RestService extends ServiceBase
{
    /**
     * X-Header containging the clients metadata hash
     */
    const HEADER_META_HASH = "X_METADATA_HASH";
    const USER_META_HASH = 'X_USERPREF_HASH';
    const DOWNLOAD_COOKIE = 'download_token';

    public static $global_server;
    public static $global_request;
    public static $global_get;
    public static $global_post;
    public static $global_http_raw_post_data;

    public $user;
    /**
     * The request headers
     * @var array
     */

    public $request_headers = array();

    public $platform = 'base';

    /**
     * The response headers that will be sent
     * @var RestResponse
     */
    protected $response = null;

    /**
     * The minimum version accepted
     * @var integer
     */
    protected $min_version = 10;

    /**
     * The maximum version accepted
     * @var integer
     */
    protected $max_version = 10;

    /**
     * An array of api settings
     * @var array
     */
    public $api_settings = array();

    /**
     * The acl action attempting to be run
     * @var string
     */
    public $action = 'view';

    /**
     * Request object
     * @var RestRequest
     */
    protected $request;

    /**
     * Get request object
     * @return RestRequest
     */
    public function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = new RestRequest(self::$global_server, self::$global_request);
        }
        return $this->request;
    }

    /**
     * Headers that have special meaning for API and should be imported into args
     * @var array
     */
    public $special_headers = array("X_TIMESTAMP");

    /**
     * Get response object
     * @return RestResponse
     */
    public function getResponse()
    {
        if (!isset($this->response)) {
            $this->response = new RestResponse(self::$global_server);
        }
        return $this->response;
    }

    /**
     * Creates the RestService object and reads in the metadata for the API
     */
    public function __construct($global_server=false, $global_request=false,
        $global_get=false, $global_post=false, $global_http_raw_post_data=false)
    {
        self::$global_server  = ($global_server  === false) ? $_SERVER  :  $global_server;
        self::$global_request = ($global_request === false) ? $_REQUEST :  $global_request;
        self::$global_get     = ($global_get     === false) ? $_GET     :  $global_get;
        self::$global_post    = ($global_post    === false) ? $_POST    :  $global_post;

        if ($global_http_raw_post_data === false) {
            if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
                self::$global_http_raw_post_data = $GLOBALS['HTTP_RAW_POST_DATA'];
            } else {
                self::$global_http_raw_post_data = array();
            }
        } else {
            self::$global_http_raw_post_data = $global_http_raw_post_data;
        }

        $apiSettings = array();
        $this->api_settings = $apiSettings;
    }

    /**
     * This function executes the current request and outputs the response directly.
     */
    public function execute()
    {
        ob_start();

        $this->response = $this->getResponse();
        try {
            $this->request = $this->getRequest();
            $this->request_headers = $this->request->request_headers;

            if ($this->min_version > $this->request->version || $this->max_version < $this->request->version) {
                throw new ServiceApiExceptionIncorrectVersion("Please change your url to reflect version between {$this->min_version} and {$this->max_version}");
            }

            $dbm = DBManagerFactory::getDatabaseManager();
            if (empty($dbm)) {
                Log::fatal('Unable to connect to Database - terminating');
                throw new ServiceApiExceptionError('Database Configuration Error');
            }

            $clientInfo = $this->authenticateUser();
            if (empty($clientInfo)) {
                throw new ServiceApiExceptionNotAuthorized("Account Not Authorized");
            }

            $route = $this->findRoute($this->request);
            if ($route == false) {
                throw new ServiceApiExceptionNoMethod('Could not find any route that accepted a path like: '.$this->request->rawPath);
            }

            // Get the request args early for use in current user api
            $argArray = $this->getRequestArgs($route);
            unset($argArray['__url']);

            $options = array();
            foreach($argArray as $k => $v) {
                if (strlen($k) > 2 && substr($k,0,2) == "__") {
                    $options[substr($k,2)] = $v;
                    unset($argArray[$k]);
                }
            }

            $headers = array();
            foreach ($this->special_headers as $header) {
                if(isset($this->request_headers[$header])) {
                    $headers[$header] = $this->request_headers[$header];
                }
            }

            if(!empty($headers)) {
                $options['_headers'] = $headers;
            }

            $this->request->setArgs($argArray)->setRoute($route);

            $apiClass = $this->loadApiClass($route);
            $apiMethod = $route['method'];

            // Customer_Id Available to All Service Instances
            $apiClass->customer_id = $clientInfo->id;
            $apiClass->credentials = $clientInfo->toArray();

            $this->handleErrorOutput('php_error_before_api');

            $result = $apiClass->$apiMethod($this, $argArray, $options);

            $this->response->setContent($result);
            $this->respond($route, $argArray);

            if (empty($route['rawReply'])) {
                $this->handleErrorOutput('php_error_after_api');
            }
        } catch ( Exception $e ) {
            $this->handleException($e);
        }

        $this->response->send();
    }

    /**
     * Gets the leading portion of the URI for a resource
     *
     * @param array|string $resource The resource to fetch a URI for as an array
     *                               of path parts or as a string
     * @return string The path to the resource
     */
    public function getResourceURI($resource, $options = array())
    {
        $this->setResourceURIBase($options);

        // Empty resources are simply the URI for the current request
        if (empty($resource)) {
            $siteUrl = Config::get('site.base_url');
            return $siteUrl . (empty($this->request)?self::$global_server['REQUEST_URI']:$this->request->getRequestURI());
        }

        if (is_string($resource)) {
            // split string into path parts
            return $this->getResourceURI(explode('/', $resource));
        } elseif (is_array($resource)) {
            $req = $this->getRequest();
            // Hacky - we're not supposed to mess with this normally, but we need it to set up route
            $req->path = $resource;
            // Logic here is, if we find a GET route for this resource then it
            // should be valid. In most cases, where there is a POST|PUT|DELETE
            // route that does not have a GET, we're not going to be handing that
            // URI out anyway, so this is a safe validation assumption.
            $req->setMethod('GET');
            $route = $this->findRoute($req);
            if ($route != false) {
                $url = $this->resourceURIBase;
                if (isset($options['relative']) && $options['relative'] == false) {
                    $url = $req->getResourceURIBase();
                }
                return $url . implode('/', $resource);
            }
        }

        return '';
    }

    /**
     * Handles exception responses
     *
     * @param Exception $exception
     */
    protected function handleException(Exception $exception)
    {
        if ( is_a($exception,"ServiceApiException") ) {
            $httpError = $exception->getHttpCode();
            $errorLabel = 'api_exception';
            $message = $exception->getMessage();
        } elseif ( is_a($exception,"OAuth2ServerException") ) {
            //The OAuth2 Server uses a slightly different exception API
            $httpError = $exception->getHttpCode();
            $errorLabel = $exception->getMessage();
            $message = $exception->getDescription();
        } else {
            $httpError = 500;
            $errorLabel = 'unknown_error';
            $message = $exception->getMessage();
        }
        if (!empty($exception->extraData)) {
            $data = $exception->extraData;
        }
        $this->response->setStatus($httpError);

        Log::error('An exception happened: ( '.$httpError.': '.$errorLabel.')'.$message);

        // For edge cases when an HTML response is needed as a wrapper to JSON
        if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'html-json') {
            if (!isset($_REQUEST['platform']) || (isset($_REQUEST['platform']) && $_REQUEST['platform'] == 'portal')) {
                $this->response->setContent($this->getHXRReturnArray($message, $httpError));
                $this->response->setType(RestResponse::JSON_HTML, true);
                $this->response->setStatus(200);

                return;
            }
        }

        // Send proper headers
        $this->response->setType(RestResponse::JSON, true);
        $this->response->setHeader("Cache-Control", "no-store");

        $replyData = array(
            'error'=>$errorLabel,
        );
        if ( !empty($message) ) {
            $replyData['error_message'] = $message;
        }
        if(!empty($data)) {
            $replyData = array_merge($replyData, $data);
        }

        $this->response->setContent($replyData);
    }

    /**
     * Handles authentication of client account
     *
     * @returns RestClientInfo $clientInfo or null if not authenticated
     */
    protected function authenticateUser()
    {
//        if (empty(self::$global_server['HTTP_API_USER']) ||
//            empty(self::$global_server['HTTP_API_INSTANCE']) ||
//            empty(self::$global_server['HTTP_API_SITE_URL'])) {
//            throw new ServiceApiExceptionNotAuthorized("Authentication Tokens Missing or Invalid");
//        }

        $userCredentials = array (
            "api_user" => self::$global_server['HTTP_API_USER'],
            "api_instance" => self::$global_server['HTTP_API_INSTANCE'],
            "api_site_url" => self::$global_server['HTTP_API_SITE_URL'],
            "ip" => self::$global_server['REMOTE_ADDR']
        );

        $authenticationManager = SystemClassLoader::getInstance('AuthenticationManager');
        $result = $authenticationManager->authenticate($userCredentials['api_user']);
        Log::debug("Authenticating Customer: [" . $userCredentials['api_user'] . "]");
        if (!$result) {
            Log::fatal("Authentication Failed - Account Not Authorized: [" . $userCredentials['api_user'] . "]");
            return null;
        }

        /*-- Track All Authorized Requests --*/
        $restClient = new RestClient();
        $clientInfo = RestClientInfo::fromArray($userCredentials);


//        try {
//            $restClient->trackClientRequest($clientInfo);
//
//            $logMessage = sprintf("CUSTOMER IDENTITY:%s FROM USER:%s INSTANCE:%s SITE_URL:%s",
//                $clientInfo->id, $clientInfo->api_user, $clientInfo->api_instance, $clientInfo->api_site_url);
//            Log::debug($logMessage);
//
//        } catch(Exception $e) {
//            return null;
//        }

        return $clientInfo;
    }

    /**
     * Sets the proper Content-Type header for the response based on either a
     * 'format' request arg or an Accept header.
     *
     * @TODO Handle Accept header parsing to determine content type
     * @access protected
     * @param array $args The request arguments
     */
    protected function setContentTypeHeader($args)
    {
        if (isset($args['format']) && $args['format'] == 'html-json') {
        } else {
            // @TODO: Handle other response types here
        }
    }

    /**
     * Sets the response type for the client
     *
     * @TODO Handle proper content disposition based on response content type
     * @TODO gzip, and possibly XML based output
     * @param array $args The request arguments
     */
    protected function setResponseType($args)
    {
        if (isset($args['format']) && $args['format'] == 'html-json' && (!isset($args['platform']) || $args['platform'] == 'portal')) {
            $this->response->setType(RestResponse::JSON_HTML);
        } else {
            $this->response->setType(RestResponse::JSON);
        }
    }

    /**
     * Set a response header
     * @param  string $header
     * @param  string $info
     * @return bool
     */
    public function setHeader($header, $info)
    {
        if (empty($this->response)) {
           return false;
        }

        return $this->response->setHeader($header, $info);
    }

    /**
     * Check if the response headers have a header set
     * @param  string $header
     * @return bool
     */
    public function hasHeader($header)
    {
        if (empty($this->response)) {
            return false;
        }

        return $this->response->hasHeader($header);
    }

    /**
     * Send the response headers
     * @return bool
     */
    public function sendHeaders()
    {
        if (empty($this->response)) {
           return false;
        }

        return $this->response->sendHeaders();
    }

    /**
     * Sets the leading portion of any request URI for this API instance
     *
     * @access protected
     */
    protected function setResourceURIBase($options = array())
    {
        // Only do this if it hasn't been done already
        if (empty($this->resourceURIBase)) {
            // Default the base part of the request URI
            $apiBase = 'api/rest.php/';

            // Check rewritten URLs AND request uri vs script name
            if (isset(self::$global_request['__url']) &&
                strpos(self::$global_server['REQUEST_URI'], self::$global_server['SCRIPT_NAME']) === false) {
                // This is a forwarded rewritten URL
                $apiBase = 'rest/';
            }

            // Get our version
            preg_match('#v(?>\d+)/#', self::$global_server['REQUEST_URI'], $m);
            if (isset($m[0])) {
                $apiBase .= $m[0];
            }

            // This is for our URI return value
            $siteUrl = '';
            if (isset($options['relative']) && $options['relative'] == false) {
                $siteUrl = Config::get('site.base_url');
            }

            // Get the file uri bas
            $this->resourceURIBase = $siteUrl . $apiBase;
        }
    }

    /**
     * Handles the response
     *
     * @param array $route  The route for this request
     * @param array  $args   The request arguments
     *
     * @return void
     */
    protected function respond($route, $args)
    {
        if (self::$global_server['REQUEST_METHOD'] == 'GET' && empty($route['noEtag'])) {
            $this->response->generateETagHeader();
        }
        
        //leaving this logic split out in case more actions on rawreply need added in the future
        if (!empty($route['rawReply'])) {
            if (self::$global_server['REQUEST_METHOD'] == 'POST') {
                $this->response->setPostHeaders();
            }
        } else {
            $this->setResponseType($args);
        }
    }

    /**
     * Generate suitable ETag for content
     *
     * This function generates the necessary cache headers for using ETags with dynamic content. You
     * simply have to generate the ETag, pass it in, and the function handles the rest.
     *
     * @param  string $etag ETag to use for this content.
     * @return bool   Did we have a match?
     */
    public function generateETagHeader($etag)
    {
        if (empty($this->response)) {
           return false;
        }

        return $this->response->generateETagHeader($etag);
    }

    /**
     * Set response to be read from file
     */
    public function fileResponse($filename)
    {
        if (empty($this->response)) {
           return false;
        }
        $this->response->setType(RestResponse::FILE)->setFilename($filename);
        $this->response->setHeader("Pragma", "public");
        $this->response->setHeader("Cache-Control", "maxage=1, post-check=0, pre-check=0");
        $this->response->setHeader("X-Content-Type-Options", "nosniff");
    }

    /**
     * Inject response object
     * @param RestResponse $resp
     */
    public function setResponse(RestResponse $resp)
    {
        $this->response = $resp;

        return $this;
    }

    /**
     * Gets the full collection of arguments from the request
     *
     * @param  array $route The route description for this request
     * @return array
     */
    protected function getRequestArgs($route)
    {
        // This loads the path variables in, so that on the /Accounts/abcd, $module is set to Accounts, and $id is set to abcd
        $pathVars = $this->request->getPathVars($route);
        $pathVars = array_merge($pathVars, $route['pathArgs']);

        $getVars = array();
        if ( !empty(self::$global_get)) {
            // This has some get arguments, let's parse those in
            $getVars = self::$global_get;
            if ( !empty($route['jsonParams']) ) {
                foreach ( $route['jsonParams'] as $fieldName ) {
                    if ( isset(self::$global_get[$fieldName])
                         && !empty(self::$global_get[$fieldName])
                         && is_string(self::$global_get[$fieldName])
                         &&  isset(self::$global_get[$fieldName]{0})
                         && ( self::$global_get[$fieldName]{0} == '{'
                               || self::$global_get[$fieldName]{0} == '[' )) {
                        // This may be JSON data
                        $jsonData = @json_decode(self::$global_get[$fieldName],true,32);
                        if (json_last_error() !== 0) {
                            // Bad JSON data, throw an exception instead of trying to process it
                            throw new ServiceApiExceptionInvalidParameter();
                        }
                        // Need to dig through this array and make sure all of the elements in here are safe
                        $getVars[$fieldName] = $jsonData;
                    }
                }
            }
        }

        $postVars = array();
        if ( isset($route['rawPostContents']) && $route['rawPostContents'] ) {
            // This route wants the raw post contents
            // We just ignore it here, the function itself has to know how to deal with the raw post contents
            // this will mostly be used for binary file uploads.
        } //else if ( !empty($_POST) ) {
            // They have normal post arguments
           // $postVars = $_POST;
          // }
        else {
            $postContents = null;
            if ( !empty(self::$global_http_raw_post_data) ) {
                $postContents = self::$global_http_raw_post_data;
            } else {
                $postContents = file_get_contents('php://input');
            }
            if ( !empty($postContents) ) {
                // This looks like the post contents are JSON
                // Note: If we want to support rest based XML, we will need to change this
                $postVars = @json_decode($postContents,true,32);
                if (json_last_error() !== 0) {
                    // Bad JSON data, throw an exception instead of trying to process it
                    throw new ServiceApiExceptionInvalidParameter();
                }
            }
        }

        // I know this looks a little weird, overriding post vars with get vars, but
        // in the case of REST, get vars are fairly uncommon and pretty explicit, where
        // the posted document is probably the output of a generated form.
        return array_merge($postVars,$getVars,$pathVars);
    }

    /**
     * Attempts to find the route for this request, API version and request method
     *
     * @param  RestRequest $req REST request data
     * @return mixed
     */
    protected function findRoute(RestRequest $request)
    {
        $version  = $request->version;
        $versionLabel = "v{$request->version}";
        $pathData = $request->path;

        $endpoint = '';
        if (count($pathData) > 0) {
            $endpoint = $pathData[0];
        }
        $map_key_method = $request->method . ":" . $endpoint;
        $map_key_any = "*" . ":" . $endpoint;

        $endpoints_map = RestEndpoints::getMap($versionLabel);

        /**
        printf("SERVICE:  %s\n", $endpoint);
        printf("METHOD:   %s\n", $request->method);
        printf("MAP_KEY_METHOD:  %s\n", $map_key_method);
        printf("MAP_KEY_ANY:  %s\n", $map_key_any);
        print_r($endpoints_map);
        **/

        Log::debug("REST Endpoint: {$request->method} $endpoint");

        $map_key = null;
        if (isset($endpoints_map[$map_key_method])) {
            $map_key = $map_key_method;
        } elseif (isset($endpoints_map[$map_key_any])) {
            $map_key = $map_key_any;
        }

        if (!empty($endpoints_map[$map_key])) {
            $className = $endpoints_map[$map_key];

            $fileName = "lib/rest/api/{$versionLabel}/{$className}.php";
            if (file_exists($fileName)) {
                include_once($fileName);
            }

            if (class_exists($className)) {
                $reflectionClass = new ReflectionClass($className);

                if (!$reflectionClass->isSubclassOf('ServiceApi')) {
                    throw new ServiceApiExceptionError("Class is not a ServiceApi: " . $className);
                }

                $obj = new $className();
                $apiSpecs = $obj->registerApiRest();

                $api = null;
                foreach ($apiSpecs AS $key => $route) {
                    if (strtoupper($route['reqType']) === $request->method) {
                        $path = $route['path'];
                        $pathVars = $route['pathVars'];
                        $methodName = $route['method'];

                        if (count($path) != count($pathData)) {
                            continue;
                        }
                        $match = true;
                        for ($i = 0; $i < count($path) && ($match); $i++) {
                            if (!($path[$i] === "?" || $path[$i] === "<resource>" || $path[$i] === $pathData[$i])) {
                                $match = false;
                                break;
                            }
                        }
                        if (!$match) {
                            continue;
                        }

                        /* We have a Match - does the Method in the Route exist on this Class ? */
                        if (!$reflectionClass->hasMethod($methodName)) {
                            throw new ServiceApiExceptionError("Method Not Found: " . $methodName . " in class " . $className);
                        }

                        $pathArgs=array();
                        for ($i = 0; $i < count($path); $i++) {
                            if ($path[$i] == "?" && count($pathVars) > $i) {
                                $pathArgs[$pathVars[$i]] = $pathData[$i];
                            }
                        }

                        $route['className'] = $className;
                        $route['pathArgs']  = $pathArgs;
                        return $route;
                    }
                }
            }
        }

        return false;
    }
}

