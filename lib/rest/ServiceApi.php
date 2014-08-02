<?php

require_once 'lib/utils/utils.php';

class ServiceApi {

    public $customer_id;
    public $credentials;
    public $_resourceName;
    public $_resourceService;

    const DefaultMaxRows = 20;

    public static $default_order_by = array(
        "dateEntered", "id"
    );
    public static $default_order_direction = array(
        "ASC", "ASC"
    );

    function __construct()
    {
        // $c = get_class($this);
        $this->_resourceName = $this->RESOURCE_NAME;
        $this->_resourceService = $this->RESOURCE_NAME . "Services";
    }

    /**
     * Handles validation of required arguments for a request
     *
     * @param array $args
     * @param array $requiredFields
     * @throws ServiceApiExceptionMissingParameter
     */
    public function requireArgs(&$args,$requiredFields = array()) {
        foreach ( $requiredFields as $fieldName ) {
            if ( !array_key_exists($fieldName, $args) ) {
                throw new ServiceApiExceptionMissingParameter('Missing parameter: '.$fieldName);
            }
        }
    }

//    public function registerApiRest() {
//        throw new ServiceApiExceptionError('missing required registerApiRest method');
//    }

    public function registerApiRest()
    {
        $api = array(
            'listResources' => array(
                'reqType' => 'GET',
                'path' => array('<resource>'),
                'pathVars' => array(),
                'method' => 'listResources',
            ),
            'createResource' => array(
                'reqType' => 'POST',
                'path'      => array('<resource>'),
                'pathVars'  => array(),
                'method' => 'createResource',
            ),
            'getResource' => array(
                'reqType' => 'GET',
                'path'      => array('<resource>', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'getResource',
            ),
            'updateResource' => array(
                'reqType' => 'PUT',
                'path'      => array('<resource>', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'updateResource',
            ),
            'deleteResource' => array(
                'reqType' => 'DELETE',
                'path'      => array('<resource>', '?'),
                'pathVars'  => array('', 'id'),
                'method' => 'deleteResource',
            ),
        );

        return $api;
    }

    /**
     * List Resources
     *
     * @param ServiceBase $api
     * @return array
     */
    public function listResources(ServiceBase $api, $params, $options=array())
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

        $service = new $this->_resourceService();
        $rows = $service->filter($filterOptions, $options);
        //printf("ROWS = %d\n",count($rows));

        return $rows;
    }

    /**
     * Retrieve Resource
     *
     * @param ServiceBase $api
     * @return array
     */
    public function getResource(ServiceBase $api, $params, $options=array())
    {
        if (!empty($params['fields'])) {
            $fieldKeys  = explode(",", $params['fields']);
        }

        $id = $params['id'];
        $resource = new $this->_resourceName();
        $success = $resource->retrieve($id, $options);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Resource Not Found");
        }
        $row = $resource->toApi($fieldKeys);

        return $row;
    }

    /**
     * Update Resource
     *
     * @param ServiceBase $api
     * @return array
     */
    public function createResource(ServiceBase $api, $params, $options=array())
    {
        unset($params['id']);
        unset($params['deleted']);
        unset($params['dateEntered']);
        unset($params['dateModified']);

        $resource = new $this->_resourceName($params);
        $resource->insert($options);

        $row = $resource->toApi();
        return $row;
    }

    /**
     * Update Resource
     *
     * @param ServiceBase $api
     * @return array
     */
    public function updateResource(ServiceBase $api, $params, $options=array())
    {
        $id = $params['id'];
        $resource = new $this->_resourceName();

        unset($params['id']);
        unset($params['deleted']);
        unset($params['dateEntered']);
        unset($params['dateModified']);

        $success = $resource->retrieve($id, $options);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Resource Not Found");
        }
        $resource->applyValues($params);
        $resource->update($options);

        $row = $resource->toApi();
        return $row;
    }

    /**
     * Delete Resource
     *
     * @param ServiceBase $api
     * @return array
     */
    public function deleteResource(ServiceBase $api, $params, $options=array())
    {
        $id = $params['id'];

        $resource = new $this->_resourceName();
        $success = $resource->retrieve($id, $options);
        if (!$success) {
            throw new ServiceApiExceptionNotFound("Resource Not Found");
        }

        $resource->markDeleted($options);
        $resource->update($options);
        // $resource->delete();

        $row = $resource->toApi();
        return $row;
    }
}
