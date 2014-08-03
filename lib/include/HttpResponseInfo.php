<?php

class HttpResponseInfo {
    public $status = 200;
    public $data = '';

    function getResult($asJson = false)
    {
        if ($asJson) {
//            $result = array(
//                "status" => $this->status,
//                "data" => $this->data,
//            );
//           return json_encode($result, true);

            return json_encode($this->data, true);
         }

        if (is_array($this->data)) {
            return print_r($this->data, true);
        }

        return $this->data; // presumably string
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setCode($status) {
        $this->setStatus($status);
    }

    function setData($data) {
        $this->data = $data;
    }
}
