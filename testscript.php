<?php

ob_start();
session_start();
header("Cache-control: private, no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
header("Cache: no-cahce");
ini_set('max_execution_time', 90000);  //30 minutes
//ini_set("memory_limit", "128M");
ini_set("memory_limit", -1);
include_once 'db_connection.php';

get_type('twitter', 'posts','postType');

function get_type($channel, $table, $searchField) {
    global $channels;
    $db = $channels["$channel"];

//    if ($table == 'posts' || $table == 'userposts') {
//        $searchField = 'postType';
//    } elseif ($table == 'engagements') {
//        $searchField = 'engagementType';
//    }
    $q = "SELECT distinct($searchField) FROM $db.$table";
    $res = select($q);
    $types = array();
    for ($i = 0; $i < count($res); $i++) {
        $types[] = $res[$i][0];
    }
    $html = '<select style="width: 89%; margin-left: 6px;">';
    if (is_array($types) && $types != array()) {
        for ($i = 0; $i < count($types); $i++) {
            if ($types[$i] != '')
                $html .= "<option value='" . $types[$i] . "'> $types[$i] </option>";
        }
    }
    $html.="</select>";
    return $html;
}

?>
