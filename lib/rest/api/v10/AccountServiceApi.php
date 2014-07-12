<?php

class AccountServiceApi extends ServiceApi
{
    public function registerApiRest()
    {
        $api = array(
            'getAccountStatus' => array(
                'reqType' => 'GET',
                'path' => array('account','status'),
                'pathVars' => array(),
                'method' => 'getAccountStatus',
            ),
            'listAccounts' => array(
                'reqType' => 'GET',
                'path' => array('account'),
                'pathVars' => array(),
                'method' => 'listAccounts',
            ),
        );

        return $api;
    }

    /**
     * get Account Activation Status
     *
     * @param ServiceBase $api
     * @return array
     */
    public function getAccountStatus(ServiceBase $api, $params)
    {
       $result = array (
           "account_id" => $this->customer_id,
           "credentials" => $this->credentials,
           "status" => "active"
       );

       Log::info("Account Service: customer={$this->customer_id}  Get Account Status");
       return $result;
    }

    /**
     * get Account Activation Status
     *
     * @param ServiceBase $api
     * @return array
     */
    public function listAccounts(ServiceBase $api, $params)
    {
        $dbm = DBManagerFactory::getDatabaseManager();
        $dbm->connect();

        $limit = 500;
        $orderBy = "api_user";
        $dir = "ASC";

        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
        if (!empty($params['order'])) {
            $orderBy = $params['order'];
        }
        if (!empty($params['dir'])) {
            $dir = $params['dir'];
        }

        $rows = array();

        try {
            $sql  = "SELECT * FROM client";
            $sql .= " ORDER by $orderBy $dir LIMIT $limit";
            $result = $dbm->query($sql);
            if ($result) {
                while ($row = $dbm->fetchAssoc($result)) {
                    $rows[] = $row;
                }
                $dbm->freeQueryResult($result);
            }

            Log::info("Account Service: customer={$this->customer_id}  list Accounts");

        } catch (Exception $e) {
            Log::error($e->getMessage()); // Do Not Return - Must Release Locks
        }

        return $rows;
    }
}
