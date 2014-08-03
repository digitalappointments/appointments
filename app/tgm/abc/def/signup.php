<?php
// if (($httpRequestInfo->requestMethod == 'POST' || $httpRequestInfo->requestMethod == 'PUT') &&
//     strpos($httpRequestInfo->accept, "json") !== false &&
//     !empty($httpRequestInfo->payload)
// ) {
//     $httpResponseInfo->setStatus(200);
//     $httpResponseInfo->setData($httpRequestInfo->payload);
//     return;
// }   

$argv = $httpRequestInfo->getArgs();

$httpResponseInfo->setStatus(200);

addResponse("IN:  abc/def/signup.php");
addResponse("METHOD: {$httpRequestInfo->requestMethod}");
addResponse("ARGS:");
addResponse(print_r($argv,true));
addResponse("PAYLOAD:");
addResponse(print_r($httpRequestInfo->payload,true));   

if (strpos($httpRequestInfo->accept, "json") === false) {
   printf("<PRE>\n");
?>

   <img src="http://localhost:8888/tgm/app/tgm/images/corn_medium.jpg">

<?php
}


function addResponse($respLine) {
    global $httpResponseInfo;
    if (empty($httpResponseInfo->data)) {
        $httpResponseInfo->data = array();
    }
    $httpResponseInfo->data[] = $respLine;
}
