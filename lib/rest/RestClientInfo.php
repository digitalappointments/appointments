<?php

class RestClientInfo
{
    public $id;
    public $api_user;
    public $api_instance;
    public $api_site_url;
    public $ip;
    public $request_count;
    public $last_request;
    public $date_entered;
    public $date_modified;

    public static function fromArray(array $params)
    {
        $clientInfo = new RestClientInfo();
        $clientInfo->id = empty($params['id']) ? '' : $params['id'];
        $clientInfo->api_user = empty($params['api_user']) ? '' : $params['api_user'];
        $clientInfo->api_instance = empty($params['api_instance']) ? '' : $params['api_instance'];
        $clientInfo->api_site_url = empty($params['api_site_url']) ? '' : $params['api_site_url'];
        $clientInfo->ip = empty($params['ip']) ? '' : $params['ip'];
        $clientInfo->request_count = empty($params['request_count']) ? '0' : $params['request_count'];
        $clientInfo->last_request = empty($params['last_request']) ? '0' : $params['last_request'];
        $clientInfo->date_entered = empty($params['date_entered']) ? '' : $params['date_entered'];
        $clientInfo->date_modified = empty($params['date_modified']) ? '' : $params['date_modified'];
        return $clientInfo;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'api_user' => $this->api_user,
            'api_instance' => $this->api_instance,
            'api_site_url' => $this->api_site_url,
            'ip' => $this->ip,
            'request_count' => $this->request_count,
            'last_request' => $this->last_request,
            'date_entered' => $this->date_entered,
            'date_modified' => $this->date_modified
        );
    }
}
