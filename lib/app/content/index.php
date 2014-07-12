<?php
$status = 404;
ob_start();
if (!empty($_REQUEST['f']) && file_exists($_REQUEST['f'] . ".php")) {
    include_once($_REQUEST['f'] . ".php");
    $status = 200;
}
$data = ob_get_clean();
$result = array(
    "status" => $status,
    "data" => $data,
);
echo json_encode($result);
?>
