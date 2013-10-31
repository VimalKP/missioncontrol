<?php

ob_start();
header("Cache-control: private, no-cache");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
header("Cache: no-cahce");
ini_set('max_execution_time', 90000);  //30 minutes
//ini_set("memory_limit", "128M");
ini_set("memory_limit", -1);
//restrict_unknown();
$isLocal = false;
//define our connection parameter
if ($isLocal == true) {
    define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
    define('DB_SERVER_USERNAME', 'root');
    define('DB_SERVER_PASSWORD', '');
    //define('DB_SERVER_PASSWORD', 'dmbdemo_unoapp');
//    define('DB_DATABASE', 'youtube_scrapper');
    define('DB_DATABASE', 'agentsqa');
//    define('DB_DATABASE', 'facebook_agent_new');
//    date_default_timezone_set('America/Toronto');
    $conn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die("Unable to connect with server");
    mysql_select_db(DB_DATABASE) or die("Unable to select database");
    //THE FOLDER OF YOUR UNOAPP
//	DEFINE (UNOAPP_FOLDER , 'unoapp-staging.git/');
} else {
//    echo 'hi';
//    DEFINE('SERVER_PREFIX_FOR_DATABASE', 'dmbdemo_');
    define('DB_SERVER', '162.144.44.70'); // eg, localhost - should not be empty for productive servers
    define('DB_SERVER_USERNAME', 'dmbdemo');
    define('DB_SERVER_PASSWORD', 'demo$$99');
    define('DB_DATABASE', 'dmbdemo_missioncontrol');
    define('DB_AgentQA_DATABASE', 'dmbdemo_AgentQA');
//    date_default_timezone_set('America/Toronto');
//    define('DB_WEBTOOL_DATABASE', SERVER_PREFIX_FOR_DATABASE . 'webtool'); // added for method  adding record to both database 
    // date_default_timezone_set('America/Toronto');
    $conn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die("Unable to connect with server");
    mysql_select_db(DB_DATABASE) or die("Unable to select database");
    
    //THE FOLDER OF YOUR UNOAPP
//    DEFINE(UNOAPP_FOLDER, 'unoapp/');
    /* define('DB_SERVER', 'dmbdemo.com'); // eg, localhost - should not be empty for productive servers
      define('DB_SERVER_USERNAME', 'dmbdemo');
      define('DB_SERVER_PASSWORD', 'demo$$99');
      define('DB_DATABASE', 'dmbdemo_unoapp');
      define('DB_WEBTOOL_DATABASE',  'dmbdemo_webtool'); */ // added for method  adding record to both database     
}
define("API_GRAPH", 'https://graph.facebook.com/');
define("ANTELOPE_SITE_URL_REMOTE", "http://" . $_SERVER['HTTP_HOST'] . "/unoappsocial/");
include_once 'includes/functions.php';
$channels = array(
    'facebook' => 'dmbdemo_facebook',
    'instagram' => 'dmbdemo_instagram',
    'twitter' => 'dmbdemo_twitter',
    'youtube' => 'dmbdemo_youtube'
);
$channels_icon = array(
    'facebook' => 'images/facebook.png',
    'Instagram' => 'images/instagram.png',
    'twitter' => 'images/twitter.png',
    'youtube' => 'images/you-tube.png'
);
ob_end_flush();
?>