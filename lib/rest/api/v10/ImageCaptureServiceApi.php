<?php

require_once("lib/services/ImageCaptureService/ImageCaptureService.php");

class ImageCaptureServiceApi extends ServiceApi
{
    public function registerApiRest()
    {
        $api = array(
            'captureImage' => array(
                'reqType' => 'POST',
                'path' => array('imagecapture'),
                'pathVars' => array('', ''),
                'method' => 'captureImage',
            ),
        );

        return $api;
    }

    /**
     * Create Image From HTML
     *
     * @param ServiceBase $api
     * @param $params
     * @return array
     * @throws ServiceApiExceptionError
     */
    public function captureImage(ServiceBase $api, $params)
    {
        if (empty($params['html_body']) && empty($params['url'])) {
            throw new ServiceApiExceptionMissingParameter('Missing one of Required Parameters: html_body, url');
        }

        $imageCapture = new ImageCaptureService($this->customer_id);
        if (!empty($params['url'])) {
            $result = $imageCapture->imageFromUrl($params['url'], $params);
        } else {
            if (!empty($params['html_body']) && !$this->isBase64Encoded($params['html_body'])) {
                throw new ServiceApiExceptionInvalidParameter('Unexpected Format: html_body not base64 encoded');
            }
            $html_body = base64_decode($params['html_body']);
            $result = $imageCapture->imageFromHtml($html_body, $params);
        }

        Log::info("Image Capture Service: customer={$this->customer_id}  Template Thumbnail Generated");

        return $result;
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

    /**
     * Check to see whether data is already base64Encoded so we don't
     * double encode
     * @param string Data to Check
     * @return bool  True if Base64Encoded
     */
    protected function isBase64Encoded($data) {
        if (base64_encode(base64_decode($data)) === $data){
            return true;
        } else {
            return false;
        }
    }
}
