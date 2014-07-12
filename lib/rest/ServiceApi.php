<?php

require_once 'lib/utils/utils.php';

class ServiceApi {

    public $customer_id;
    public $credentials;

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

    public function registerApiRest() {
        throw new ServiceApiExceptionError('missing required registerApiRest method');
    }
}
