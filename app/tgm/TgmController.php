<?php

$ROOT_DIR = dirname(__FILE__);

class TgmController extends BaseController
{
    public $user;
    public $account;

    public function __construct()
    {
        $stop = "HERE";
    }

    public function run(HttpRequestInfo &$httpRequestInfo, HttpResponseInfo &$httpResponseInfo)
    {
        // printf("<PRE>\n%s<br>\n",print_r($httpRequestInfo, true));

        $stop = "Here";

        if (!empty($httpRequestInfo->requestPath)) {

            $file = $GLOBALS['ROOT_DIR'] . "/" . $httpRequestInfo->requestPath;

            if (file_exists($file)) {
                $path_parts = pathinfo($file);
                $ext = $path_parts['extension'];
                if ($ext == 'php') {
                    include_once($file);
                    return;
                }

//                header('Content-Description: File Transfer');
//                header('Content-Type: application/octet-stream');
//                header('Content-Disposition: attachment; filename='.basename($file));
//                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }

            printf("<PRE>\n");
            printf("TGMController:   requestPath = %s\n",$httpRequestInfo->requestPath);
            printf("TGMController:   requestMethod = %s\n",$httpRequestInfo->requestMethod);
            printf("TGMController:   requestURI = %s\n",$httpRequestInfo->requestURI);
            printf("TGMController:   queryString = %s\n",$httpRequestInfo->queryString);
            printf("TGMController:   getVars:__url = %s\n",$httpRequestInfo->getVars['__url']);

            printf("TGMController:   File = %s\n",$file);

            return;
        }


//        try {
//
//            $dbm = DBManagerFactory::getDatabaseManager();
//            if (empty($dbm)) {
//                Log::fatal('Unable to connect to Database - terminating');
//                throw new BasicException('Database Configuration Error');
//            }
//
//        } catch (Exception $e) {
//            $this->handleException($e);
//        }

        ob_start();
        if (!empty($_REQUEST['f'])) {
            $contentDir = "app/tgm/content/";
            $contentFile = $contentDir . $_REQUEST['f'] . ".php";
            if (file_exists($contentFile)) {
                include_once($contentFile);
            }
        } else {
            $pageDir  = "app/tgm/html/";
            $pageFile = $pageDir . "index.php";
            if (file_exists($pageFile)) {
                include_once($pageFile);
            }
        }
        $data = ob_get_clean();

        $httpResponseInfo->setStatus(200);
        $httpResponseInfo->setData($data);
    }
}
