<?php
include_once("lib/model/account/AccountServices.php");

class AccountsApi extends ServiceApi
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
            'listAccounts' => array(
                'reqType' => 'GET',
                'path' => array('accounts'),
                'pathVars' => array(),
                'method' => 'listAccounts',
            ),
            'createAccount' => array(
                'reqType' => 'POST',
                'path'      => array('accounts'),
                'pathVars'  => array(),
                'method' => 'createAccount',
            ),
            'getAccount' => array(
                'reqType' => 'GET',
                'path'      => array('accounts', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'getAccount',
            ),
            'updateAccount' => array(
                'reqType' => 'PUT',
                'path'      => array('accounts', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'updateAccount',
            ),
            'deleteAccount' => array(
                'reqType' => 'DELETE',
                'path'      => array('accounts', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'deleteAccount',
            ),
        );

        return $api;
    }

    /**
     * List Accounts
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

    /**
     * Retrieve Account
     *
     * @param ServiceBase $api
     * @return array
     */
    public function getAccount(ServiceBase $api, $params)
    {
        $options = array();
        if (!empty($params['fields'])) {
            $fieldKeys  = explode(",", $params['fields']);
        }

        $id = $params['id'];
        $account = new Account();
        $success = $account->retrieve($id);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Account Not Found");
        }
        $row = $account->toApi($fieldKeys);

        return $row;
    }

    /**
     * Update Account
     *
     * @param ServiceBase $api
     * @return array
     */
    public function createAccount(ServiceBase $api, $params)
    {
        unset($params['id']);
        unset($params['deleted']);
        unset($params['dateEntered']);
        unset($params['dateModified']);

        $account = new Account($params);
        $account->insert();
        // $account->delete();

        $row = $account->toApi();
        return $row;
    }

    /**
     * Update Account
     *
     * @param ServiceBase $api
     * @return array
     */
    public function updateAccount(ServiceBase $api, $params)
    {
        $id = $params['id'];
        $account = new Account();

        unset($params['id']);
        unset($params['deleted']);
        unset($params['dateEntered']);
        unset($params['dateModified']);

        $success = $account->retrieve($id);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Account Not Found");
        }
        $account->applyValues($params);
        $account->update();

        $row = $account->toApi();
        return $row;
    }

    /**
     * Delete Account
     *
     * @param ServiceBase $api
     * @return array
     */
    public function deleteAccount(ServiceBase $api, $params)
    {
        $id = $params['id'];
        $account = new Account();
        $success = $account->retrieve($id);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Account Not Found");
        }

        $account->markDeleted();
        $account->update();
        // $account->delete();

        $row = $account->toApi();
        return $row;
    }
}
