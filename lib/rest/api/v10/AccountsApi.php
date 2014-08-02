<?php
include_once("lib/model/account/AccountServices.php");

class AccountsApi extends ServiceApi
{
    public $RESOURCE_NAME = 'Account';

    public function registerApiRest()
    {
        $api = array(
            'listAccounts' => array(
                'reqType' => 'GET',
                'path' => array('accounts'),
                'pathVars' => array(),
                'method' => 'listAccounts',
            ),
//            'createAccount' => array(
//                'reqType' => 'POST',
//                'path'      => array('accounts'),
//                'pathVars'  => array(),
//                'method' => 'createAccount',
//            ),
            'getAccount' => array(
                'reqType' => 'GET',
                'path'      => array('accounts', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'getAccount',
            ),
//            'updateAccount' => array(
//                'reqType' => 'PUT',
//                'path'      => array('accounts', '?'),
//                'pathVars'  => array('', 'id'),
//                'method' => 'updateAccount',
//            ),
//            'deleteAccount' => array(
//                'reqType' => 'DELETE',
//                'path'      => array('accounts', '?'),
//                'pathVars'  => array('', 'id'),
//                'method' => 'deleteAccount',
//            ),
        );

        return array_merge($api, parent::registerApiRest());
    }

    /**
     * List Accounts
     *
     * @param ServiceBase $api
     * @return array
     */
    public function listAccounts(ServiceBase $api, $params, $options=array())
    {
        /* Add/Remove/Update any params */

        $rows = parent::listResources($api, $params, $options);

        /* Modify Result */

        $rows[] = array(
            'name' => 'Green Bay Packers',
            'description' => 'I have been added - Not really an Account Record',
        );
        return $rows;
    }

    /**
     * Retrieve Account
     *
     * @param ServiceBase $api
     * @return array
     */
    public function getAccount(ServiceBase $api, $params, $options=array())
    {
        /* Add/Remove/Update any params */

        $row = parent::getResource($api, $params, $options);

        /* Modify Result */

        return $row;
    }

}
