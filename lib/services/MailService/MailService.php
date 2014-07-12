<?php

require_once("lib/model/MailServiceSendParameters.php");

/**
 * This class is the Abstract Base Class for the Mail Services that will
 * interact with the various Third Party Mail Service Providers
 *
 * @interface
 */
abstract class MailService
{
    protected $dbm;
    protected $coreUrl;

    function __construct() {
        $this->coreUrl = Config::get('site.core_url');

        $this->dbm = DBManagerFactory::getDatabaseManager();
        $this->dbm->connect();
    }

    /**
     * @abstract
     * @access public
     * @param string $api_user required
     * @param string $api_pass required
     */
    abstract public function setServiceAccountInfo($api_user, $api_pass);

    /**
     * @abstract
     * @access public
     * @param string $customer_id required
     * @param MailServiceSendParameters $sendParams required
     */
    abstract public function send($customer_id, MailServiceSendParameters $sendParams);

}
