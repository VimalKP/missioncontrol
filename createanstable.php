<?php

include_once 'db_connection.php';
$query = "SELECT p.channel,p.handle,a.qa_id,a.passes FROM (
 SELECT 'facebook' as 'channel',handle 
 FROM dmbdemo_facebook_agent.brands
) p INNER JOIN (
 SELECT qa_id,channel,passes 
 FROM dmbdemo_instagram.agentsqa
) a ON binary p.channel = binary a.channel

UNION

SELECT p.channel,p.handle,a.qa_id,a.passes FROM (
 SELECT 'Instagram' as 'channel',handle 
 FROM dmbdemo_instagram.brands
) p INNER JOIN (
 SELECT qa_id,channel,passes 
 FROM dmbdemo_instagram.agentsqa
) a ON binary p.channel = binary a.channel

UNION

SELECT p.channel,p.handle,a.qa_id,a.passes FROM (
 SELECT 'twitter' as 'channel',handle 
 FROM dmbdemo_twitter_agent_standard.brands
) p INNER JOIN (
 SELECT qa_id,channel,passes 
 FROM dmbdemo_instagram.agentsqa
) a ON binary p.channel = binary a.channel

UNION

SELECT p.channel,p.handle,a.qa_id,a.passes FROM (
 SELECT 'youtube' as 'channel',handle 
 FROM dmbdemo_youtube_scrapper.brands
) p INNER JOIN (
 SELECT qa_id,channel,passes 
 FROM dmbdemo_instagram.agentsqa
) a ON binary p.channel = binary a.channel";
//$result=  mysql_query($query);
$q = "CREATE TABLE IF NOT EXISTS answers (`ans_id` bigint(20) NOT NULL AUTO_INCREMENT,PRIMARY KEY (ans_id))  $query";
//$q="CREATE TABLE answers (ans_id INT) $query";
//$result = mysql_query($q);
if (!mysql_query($q)) {
    die('Error: ' . mysql_error($conn));
}
//echo mysql_error($result);
//echo mysql_insert_id();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
