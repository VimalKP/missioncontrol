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
include('db_connection.php');
restrict_unknown();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Mission Control</title>
        <link href="css/style.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">
        <script type="text/javascript"src="js/jquery.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {

                //Default Action
                $(".tab_content").hide(); //Hide all content
                $("ul.tabs li:first").addClass("active selected").show(); //Activate first tab
                $(".tab_content:first").show(); //Show first tab content
	
                //On Click Event
                $("ul.tabs li").click(function() {
                    var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
                    var divname=activeTab.replace("#","");
                    $("ul.tabs li").removeClass("active selected"); //Remove any "active" class
                    $("ul.tab-nav li").removeClass("active"); //Remove any "active" class
                    $("ul.tab-nav li a").removeClass("selected"); //Remove any "active" class
                    $(this).addClass("active selected"); //Add "active" class to selected tab
                    $(".tab_content").hide(); //Hide all tab content
                    $(activeTab).fadeIn(); //Fade in the active content
                    var liselected=$("#"+divname).find("ul.tab-nav li:first").addClass("active").text().toLowerCase();
                    if(liselected!="")
                        $("#"+liselected).show();
                    $("#"+divname).find("ul.tab-nav").find(".active").find("a").addClass("selected");
                    return false;
                });
	
                $("ul.tab-nav li").click(function() {
                    var prevselect=$("ul.tab-nav").find(".active").find("a").removeClass("selected").text().toLowerCase();//remove selected class and get value for hide table for perticular tab
                    if(prevselect!="")
                        $("#"+prevselect).hide();
                    $("ul.tab-nav li").removeClass("active"); //Remove any "active" class
                    $(this).addClass("active"); //Add "active" class to selected tab
                    var activeTab = $(this).find("a").addClass("selected").text().toLowerCase();//get value for show table for perticular tab
                    $("#"+activeTab).show();//show tab	
			
                });
            });
            function logout(){
                $.ajax({
                    url:'login_ops.php',
                    type: 'post',
                    data: {'action':'logout'},
                    success:function(data){
                        window.location.href="index.html";
                        return false;
                    }
                });
            }
        </script>
    </head>

    <body>
        <div class="container">
            <h1>UnoApp/ Antelop Social Intelligence</h1>
            <div style="float: right">Welcome, <label><?= $_SESSION['qa_contact_name'] ?></label>
                <input type="button" value="Logout" class="" onclick="javascript:logout();">
            </div>
            <h3>Mission Control</h3>
            <ul class="tabs">
                <li><a href="#tab1">Mission Control</a></li>
                <li><a href="#tab2">Manage Pages</a></li>
                <li><a href="#tab3">Qa</a></li>
                <li><a href="#tab4">Modules</a></li>
            </ul>
            <div class="tab_container">
                <div id="tab1" class="tab_content" style="padding: 15px;">
                    <h4>Database Overview</h4>
                    <div class="first-row">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="">
                                <td>Channels</td>
                                <td>Tracked Accounts</td>
                                <td>Users</td>
                                <td>Posts</td>
                                <td>Engagements</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="odd">
                                <td>Facebook</td>
                                <td>61</td>
                                <td>101048</td>
                                <td>17134</td>
                                <td>176613</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="even">
                                <td>Twitter</td>
                                <td>34</td>
                                <td>136821</td>
                                <td>5387</td>
                                <td>169739</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="odd">
                                <td>Youtube</td>
                                <td>8</td>
                                <td>171595</td>
                                <td>7580</td>
                                <td>133803</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="even">
                                <td>Instagram</td>
                                <td>39</td>
                                <td>53146</td>
                                <td>7631</td>
                                <td>142156</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="odd">
                                <td>Pinterest</td>
                                <td>8</td>
                                <td>171595</td>
                                <td>7580</td>
                                <td>133803</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="even">
                                <td>Tumblr</td>
                                <td>39</td>
                                <td>53146</td>
                                <td>7631</td>
                                <td>142156</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="odd">
                                <td>Total</td>
                                <td>8</td>
                                <td>171595</td>
                                <td>7580</td>
                                <td>133803</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="tab2" class="tab_content" style="padding: 1px;">
                    <div class="tab-nav">
                        <ul class="tab-nav">
                            <li><a href="#facebook">Facebook</a></li>
                            <li><a href="#twitter">Twitter</a></li>
                            <li><a href="#youtube">Youtube</a></li>
                            <li><a href="#instagram">Instagram</a></li>
                            <li><a style="border:none;" href="#pinterest">Pinterest</a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div id="facebook" class="tab_content">
                        <div class="facebook">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="">
                                    <td>Brand</td>
                                    <td style="width: 20%;">Facebook URL</td>
                                    <td>Tracked Since</td>
                                    <td>Last Updated</td>
                                    <td>Start Fans</td>
                                    <td>End Fans</td>
                                    <td>Fan Growth</td>
                                    <td>Fan % Change</td>
                                    <td>Posts</td>
                                    <td>Engagements</td>
                                    <td>Engaged Users</td>
                                    <td>Edit/Delete</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-odd">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-even">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-odd">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-even">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-odd">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-even">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="facebook-odd">
                                    <td>Coamo TV</td>
                                    <td style="width: 20%;">http://www.facebook.com/CoamoTV</td>
                                    <td>05-24-2012</td>
                                    <td>05-21-2013</td>
                                    <td>23659</td>
                                    <td>36129</td>
                                    <td>10470</td>
                                    <td>40.80%</td>
                                    <td>528</td>
                                    <td>39981</td>
                                    <td>8407</td>
                                    <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div id="twitter" class="tab_content">
                        <div class="first-row">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="">
                                    <td>Channels</td>
                                    <td>Tracked Accounts</td>
                                    <td>Users</td>
                                    <td>Posts</td>
                                    <td>Engagements</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Facebook</td>
                                    <td>61</td>
                                    <td>101048</td>
                                    <td>17134</td>
                                    <td>176613</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Twitter</td>
                                    <td>34</td>
                                    <td>136821</td>
                                    <td>5387</td>
                                    <td>169739</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Youtube</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Instagram</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Pinterest</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Tumblr</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div> 
                          <!--<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr class="odd">
                    <td>Total</td>
                    <td>8</td>
                    <td>171595</td>
                    <td>7580</td>
                    <td>133803</td>
                  </tr>
                </table>--> 
                        </div>
                    </div>
                    <div id="youtube" class="tab_content">
                        <div class="first-row">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="">
                                    <td>Channels</td>
                                    <td>Tracked Accounts</td>
                                    <td>Users</td>
                                    <td>Posts</td>
                                    <td>Engagements</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Facebook</td>
                                    <td>61</td>
                                    <td>101048</td>
                                    <td>17134</td>
                                    <td>176613</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Twitter</td>
                                    <td>34</td>
                                    <td>136821</td>
                                    <td>5387</td>
                                    <td>169739</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Youtube</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Instagram</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Pinterest</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Tumblr</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Total</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div id="instagram" class="tab_content">
                        <div class="first-row">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="">
                                    <td>Channels</td>
                                    <td>Tracked Accounts</td>
                                    <td>Users</td>
                                    <td>Posts</td>
                                    <td>Engagements</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Facebook</td>
                                    <td>61</td>
                                    <td>101048</td>
                                    <td>17134</td>
                                    <td>176613</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Twitter</td>
                                    <td>34</td>
                                    <td>136821</td>
                                    <td>5387</td>
                                    <td>169739</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Youtube</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Instagram</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Pinterest</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Tumblr</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Total</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div id="pinterest" class="tab_content">
                        <div class="first-row">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="">
                                    <td>Channels</td>
                                    <td>Tracked Accounts</td>
                                    <td>Users</td>
                                    <td>Posts</td>
                                    <td>Engagements</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Facebook</td>
                                    <td>61</td>
                                    <td>101048</td>
                                    <td>17134</td>
                                    <td>176613</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Twitter</td>
                                    <td>34</td>
                                    <td>136821</td>
                                    <td>5387</td>
                                    <td>169739</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Youtube</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Instagram</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Pinterest</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="even">
                                    <td>Tumblr</td>
                                    <td>39</td>
                                    <td>53146</td>
                                    <td>7631</td>
                                    <td>142156</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr class="odd">
                                    <td>Total</td>
                                    <td>8</td>
                                    <td>171595</td>
                                    <td>7580</td>
                                    <td>133803</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div style="margin:10px 0;">
                        <div class="btn-facebook-pages"><a href="#">Add Facebook Pages</a></div>
                        <div class="paginate"><a onclick="getPageData(1)" href="#"> &lt;&lt; </a><a onclick="getPageData(5)" href="#"> &lt; </a><a onclick="getPageData(1)" href="#">1</a><a onclick="getPageData(2)" href="#">2</a><a onclick="getPageData(3)" href="#">3</a><a onclick="getPageData(4)" href="#">4</a><a onclick="getPageData(5)" href="#">5</a><span class="current">6</span><a onclick="getPageData(7)" href="#">7</a><a onclick="getPageData(8)" href="#">8</a><a onclick="getPageData(9)" href="#">9</a>...<a onclick="getPageData(41)" href="#">41</a><a onclick="getPageData(42)" href="#">42</a><a onclick="getPageData(7)" href="#"> &gt; </a><a onclick="getPageData(42)" href="#"> &gt;&gt; </a></div>
                    </div>
                </div>
                <div id="tab3" class="tab_content" style="padding: 1px;">
                    <div class="tab-nav">
                        <ul class="tab-nav">
                            <li><a href="#facebook">Facebook</a></li>
                            <li><a href="#twitter">Twitter</a></li>
                            <li><a href="#youtube">Youtube</a></li>
                            <li><a href="#instagram">Instagram</a></li>
                            <li><a style="border:none;" href="#pinterest">Pinterest</a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div class="qa">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="">
                                <td>Network</td>
                                <td>Last Checked</td>
                                <td>Varified By</td>
                                <td>Score</td>
                                <td>Status</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-even">
                                <td style="text-align: left;"><img src="images/facebook.png" alt="" />Facebook</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="green large-font">6/6</td>
                                <td class="green"><img src="images/pass-icon.png" alt="" />Pass</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-odd">
                                <td style="text-align: left;"><img src="images/twitter.png" alt="" />Twitter</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="dark-yellow large-font">3/4</td>
                                <td class="dark-yellow"><img src="images/varification-icon.png" alt="" />Need Varification</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-even">
                                <td style="text-align: left;"><img src="images/you-tube.png" alt="" />Facebook</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="red large-font">1/4</td>
                                <td class="red"><img src="images/fail-icon.png" alt="" />Fail</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-odd">
                                <td style="text-align: left;"><img src="images/instagram.png" alt="" />Twitter</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="dark-yellow large-font">3/4</td>
                                <td class="dark-yellow"><img src="images/varification-icon.png" alt="" />Need Varification</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-even">
                                <td style="text-align: left;"><img src="images/pinterest.png" alt="" />Facebook</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="red large-font">1/4</td>
                                <td class="red"><img src="images/fail-icon.png" alt="" />Fail</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="qa-odd">
                                <td style="text-align: left;"><img src="images/future-network.png" alt="" />Twitter</td>
                                <td>10:30 AM June 20, 2013</td>
                                <td>Trey anastasio</td>
                                <td class="dark-yellow large-font">3/4</td>
                                <td class="dark-yellow"><img src="images/varification-icon.png" alt="" />Need Varification</td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin:10px 0 0 15px;">Send Email Updates To:
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div class="submit-email">
                        <div style="float:left; width:86%; margin-left: 15px;">
                            <input type="email">
                        </div>
                        <div class="btn-save"><a href="#">save</a></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div id="tab4" class="tab_content" style="padding: 5px;">
                    <div class="module-top">
                        <div class="module-search-box">
                            <input type="search" placeholder="search">
                        </div>
                        <div class="drop-down">
                            <div class="styled-select">
                                <select>
                                    <option>Channels</option>
                                    <option>Facebook</option>
                                    <option>Twitter</option>
                                    <option>Youtube</option>
                                    <option>Pinterest</option>
                                    <option>Instagram</option>
                                </select>
                            </div>
                            <div class="styled-select">
                                <select>
                                    <option>Objectives</option>
                                    <option>Enganement</option>
                                    <option>Growth</option>
                                    <option>Customer Service</option>
                                    <option>Benchmarks</option>
                                    <option>Customer Insights</option>
                                </select>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="modules">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="">
                                <td style="width: 10%;">Module Name</td>
                                <td>Channel</td>
                                <td>Section</td>
                                <td style="width: 27%;">Question</td>
                                <td>Takeaway(s)</td>
                                <td>Edit/Delete</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-odd">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-even">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-odd">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-even">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-odd">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="modules-even">
                                <td style="width: 10%;">Customer Services</td>
                                <td>Facebook</td>
                                <td>Community Mgmt</td>
                                <td style="width: 27%;">How are brands responding inquiries on facebook?</td>
                                <td>3</td>
                                <td><a href="#"><img src="images/icon-edit.png" alt=""></a><a href="#"><img src="images/icon-delete.png"></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </body>
</html>