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
$channels = array(
    'facebook' => 'dmbdemo_facebook_agent',
    'Instagram' => 'dmbdemo_instagram',
    'twitter' => 'dmbdemo_twitter_agent_standard',
    'youtube' => 'dmbdemo_youtube_scrapper'
);
restrict_unknown();
echo json_encode($_SESSION);
?>
        