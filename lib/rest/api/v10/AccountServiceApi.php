<?php
include_once("lib/model/account/AccountServices.php");

class AccountServiceApi extends ServiceApi
{
    const DefaultMaxRows = 20;

    public static $default_order_by = array(
        "name", "id"
    );
    public static $default_order_direction = array(
        "ASC", "ASC"
    );

    public function registerApiRest()
    {
        $api = array(
            'getAccountStatus' => array(
                'reqType' => 'GET',
                'path' => array('accounts','status'),
                'pathVars' => array(),
                'method' => 'getAccountStatus',
            ),
            'listAccounts' => array(
                'reqType' => 'GET',
                'path' => array('accounts'),
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
        if (empty($params['order_by'])) {
            $orderBy  = static::$default_order_by;
            $orderDir = static::$default_order_direction;
        } else {
            $orderBy = explode(",", $params['order_by']);
            if (empty($params['order_dir'])) {
                $orderDir = array();
            } else {
                $orderDir = explode(",", $params['order_dir']);
            }
        }
        if (empty($params['max_num'])) {
            $maxNum = static::DefaultMaxRows;
        } else {
            $maxNum = intval($params['max_num']);
        }

        $filterOptions = array(
            "order_by"  => $orderBy,
            "order_dir" => $orderDir,
            "max_num" => $maxNum,
        );

        if (!empty($params['fields'])) {
            $fieldFilter  = explode(",", $params['fields']);
            if (count($fieldFilter) > 0) {
                $filterOptions['fields'] = $fieldFilter;
            }
        }

        $accountServices = new AccountServices();
        $rows = $accountServices->filter($filterOptions);
        //printf("ROWS = %d\n",count($rows));

        return $rows;
    }
}
