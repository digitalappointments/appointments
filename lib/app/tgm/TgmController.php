<?php

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
            $contentDir = "lib/app/tgm/content/";
            $contentFile = $contentDir . $_REQUEST['f'] . ".php";
            if (file_exists($contentFile)) {
                include_once($contentFile);
            }
        } else {
            $pageDir  = "lib/app/tgm/html/";
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
