<?php

require_once("lib/services/StorageService/ImageStoreService.php");

class ImagestoreServiceApi extends ServiceApi
{
    public function registerApiRest()
    {
        $api = array(
            'createImage' => array(
                'reqType' => 'POST',
                'path' => array('imagestore'),
                'pathVars' => array('', ''),
                'method' => 'createImage',
            ),
            'deleteImage' => array(
                'reqType' => 'DELETE',
                'path' => array('imagestore','?'),
                'pathVars' => array('','resource_name'),
                'method' => 'deleteImage',
            ),
            'retrieveImage' => array(
                'reqType' => 'GET',
                'path' => array('imagestore','?'),
                'pathVars' => array('','resource_name'),
                'method' => 'retrieveImage',
            ),
            'listImages' => array(
                'reqType' => 'GET',
                'path' => array('imagestore'),
                'pathVars' => array(''),
                'method' => 'listImages',
            ),
        );

        return $api;
    }

    /**
     * Store New Image Resource in Image Store
     *
     * @param ServiceBase $api
     * @param $params
     * @return array
     * @throws ServiceApiExceptionError
     */
    public function createImage(ServiceBase $api, $params)
    {
        $required_params = array('resource_name', 'mime_type', 'contents');
        $this->checkRequiredParams($required_params, $params);

        $resourceName = $params['resource_name'];
        if ($this->resourceExists($resourceName)) {
            throw new ServiceApiExceptionError('Resource Already Exists');
        }

        try {
            $storageService = new ImageStoreService($this->customer_id);
            $mimeType = $params['mime_type'];
            $contents = base64_decode($params['contents']);

            $remoteName = empty($params['local_name']) ? '' : $params['local_name'];
            $metadata = array(
                "remote_name" => $remoteName
            );

            $resource_url = $storageService->putObject($contents, $resourceName, $mimeType, $metadata);

            $result = array(
                "resource_name" => $resourceName,
                "resource_url" => $resource_url,
            );

            Log::info("Image Store Service: customer={$this->customer_id}  Image Saved to Image Library: {$resourceName}");
            return $result;
        } catch (Exception $e) {
            throw new ServiceApiExceptionError('PutObject Storage Service Failure');
        }
    }

    /**
     * Delete Image Resource from Image Store
     *
     * @param ServiceBase $api
     * @param $params
     * @return array
     * @throws ServiceApiExceptionNotFound
     * @throws ServiceApiExceptionError
     */
    public function deleteImage(ServiceBase $api, $params)
    {
        $required_params = array('resource_name');
        $this->checkRequiredParams($required_params, $params);

        $resourceName = $params['resource_name'];
        if (!$this->resourceExists($resourceName)) {
            throw new ServiceApiExceptionNotFound('Resource Not Found');
        }

        try {
            $storageService = new ImageStoreService($this->customer_id);
            $storageService->deleteObject($resourceName);
            return array(
                "resource_name" => $resourceName
            );
        } catch (Exception $e) {
            throw new ServiceApiExceptionError('DeleteObject Storage Service Failure');
        }
    }

    /**
     * Get Image Data For Specified Image From Image Store
     *
     * @param ServiceBase $api
     * @param $params
     * @return array Image Data
     * @throws ServiceApiExceptionNotFound
     * @throws ServiceApiExceptionError
     */
    public function retrieveImage(ServiceBase $api, $params)
    {
        $required_params = array('resource_name');
        $this->checkRequiredParams($required_params, $params);

        $resourceName = $params['resource_name'];
        if (!$this->resourceExists($resourceName)) {
            throw new ServiceApiExceptionNotFound('Resource Not Found');
        }

        try {
            $storageService = new ImageStoreService($this->customer_id);
            $imageInfo = $storageService->getObject($resourceName);
            return $imageInfo;
        } catch (Exception $e) {
            throw new ServiceApiExceptionError('GetObject Storage Service Failure');
        }
    }

    /**
     * List Image Data From Image Store for this Customer Account
     *
     * @param ServiceBase $api
     * @param $params
     * @return array Image Objects
     * @throws ServiceApiExceptionError
     */
    public function listImages(ServiceBase $api, $params)
    {
        try {
            $storageService = new ImageStoreService($this->customer_id);
            $imageObjects = $storageService->listObjects($params);
            return($imageObjects);
        } catch (Exception $e) {
            throw new ServiceApiExceptionError('ListObjects Storage Service Failure');
        }
    }

    /**
     * Check to see if Resource Exists
     *
     * @return bool true=Exists
     * @throws ServiceApiExceptionError
     */
    public function resourceExists($resourceName)
    {
        try {
            $storageService = new ImageStoreService($this->customer_id);
            $result = $storageService->objectExists($resourceName);
            return $result;
        } catch (Exception $e) {
            throw new ServiceApiExceptionError('GetObject Storage Service Failure');
        }
    }

    /**
     * Verify all required parameters exists
     *
     * @param $required
     * @param $actual
     * @return bool
     * @throws ServiceApiExceptionMissingParameter
     */
    protected function checkRequiredParams($required, $actual) {
        $missing = array();
        foreach ($required AS $required_param) {
            if (empty($actual[$required_param])) {
                $missing[] = $required_param;
            }
        }

        if (count($missing) > 0) {
            $fields = implode(',', $missing);
            throw new ServiceApiExceptionMissingParameter('Missing Required Parameters: ' . $fields);
        }
        return true;
    }
}
