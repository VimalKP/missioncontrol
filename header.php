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

restrict_unknown();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Mission Control</title>
        <link href="css/style.css" type="text/css" rel="stylesheet" />
        <link href="bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />
        <link href="css/bootstrap-combobox.css" media="screen" rel="stylesheet" type="text/css"/>
        <link href="font-awesome/css/font-awesome.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/DT_bootstrap.css">
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">
        <script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.js"></script>
        <!--        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
                <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
                <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
        <script type="text/javascript" src="js/application.js"></script>
        <script type="text/javascript">
            $(document).ready( function(){
                $('body').click(function(){
                    
//                    console.log('hii');
<?php
if (!isset($_SESSION['qa_contact_id'])) {
    header("Location: http://www.unoappsocial.com/missioncontrol/index.html");
//            top.location.href = "<?= ANTELOPE_SITE_URL_REMOTE 
}
?>
        });
    });
            
           
               
    //            });
        </script>
    </head>
    <body>

        <div class="container">
            <div class="row-fluid top-bar">
                <div class="logo pull-left"><a class="brand pull-left" href="#">Antelope Inc.</a>
                    <div class="clear"></div>
                </div>
                <div class="user-login pull-right">Welcome, <label><?= $_SESSION['qa_contact_name'] ?></label>
                    <span><a href="javascript:void(0)" onclick="javascript:logout();"><i class="icon-off"></i> Logout</a></span>

                </div>
            </div>
            <h2>UnoApp/ Antelope Social Intelligence</h2>
            <h4>Mission Control</h4>
            <ul class="tabs">
                <li><a id="mission_controlpage" href="home.php">Mission Control</a></li>
                <li><a id="manage_pages" href="managepages.php">Manage Pages</a></li>
                <li><a id="qa_page" href="qa.php">QA</a></li>
                <li><a id="module_page" href="modules.php">Modules</a></li>
                <li><a id="errorlog" href="errorlog.php">Error Log</a></li>
            </ul>
            <div class="clear"></div>