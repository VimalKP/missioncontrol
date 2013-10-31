<?php

ob_start();
session_start();
header("Cache-control: private, no-cache");
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
header("Cache: no-cahce");
ini_set("MAX_EXECUTION_TIME", "1800"); //30 minutes
include_once('db_connection.php');
//include_once 'includes/functions.php';
global $channels;
extract($_POST);

if ($action != '' && $action == "fetcherrorpage") {
    $contactId = $_SESSION['qa_contact_id'];
    $assoc = array();
    $result = array();

    mysql_select_db($channels[$brand]);
    $query2 = "SELECT * FROM error_log ORDER BY error_log_id DESC LIMIT  500";
    $result2 = select($query2);

    echo json_encode($result2);
}
?>