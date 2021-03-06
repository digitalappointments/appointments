<?php

class HttpClient
{
    const DELETE = 'DELETE';
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

    public static $URL_PREFIX = '';
    public static $_headers = array();

    public static function setHeaders(array $headers)
    {
        static::$_headers = $headers;
    }

    public static function addHeaders(array $headers)
    {
        static::$_headers = array_merge(static::$_headers, $headers);
    }

    public static function addHeader($hKey, $hValue)
    {
        static::$_headers[$hKey] = $hValue;
    }

    /**
     * This method uses Curl to invoke An External Service
     * @param $method
     * @param $url   - Fully Qualified
     * @param mixed $data - will be json_encoded on post/put if not null
     * @return array
     */
    public function callResource($method, $url, $data = null)
    {
        $handle = curl_init();
        $url = static::$URL_PREFIX . $url;

        $headers = array();
        foreach(static::$_headers AS $hKey => $hValue) {
            $hdr = "{$hKey}:";
            if (!empty($hValue)) {
                $hdr .= " {$hValue}";
            }
            $headers[] = $hdr;
        }

        $headers[] = 'Expect:';
        $headers[] = 'Accept: application/json';

        curl_setopt($handle, CURLOPT_URL, $url);
        if (count($headers) > 0) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        switch ($method) {
            case static::POST:
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case static::PUT:
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case static::DELETE:
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($data)) {
                    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }

        $result = curl_exec($handle);
        list($responseHeaders, $raw) = explode("\r\n\r\n", $result, 2);
        $response = json_decode($raw, true);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        return array(
            'code' => $code,
            'data' => $response,
            'raw' => $raw,
            'response_headers' => $responseHeaders,
        );
    }
}
