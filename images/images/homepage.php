<?php

include_once 'db_connection.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */
$channels = array(
    'facebook' => 'dmbdemo_facebook_agent',
    'Instagram' => 'dmbdemo_instagram',
    'twitter' => 'dmbdemo_twitter_agent_standard',
    'youtube' => 'dmbdemo_youtube_scrapper'
);

$query = "INSERT IGNORE INTO answers (channel,handle,qa_id,passed)

					SELECT p.channel,p.handle,a.qa_id,a.passed FROM (
						SELECT '%s' as 'channel',handle 
						FROM %s.brands
					) p INNER JOIN (
						SELECT qa_id,channel,passed 
						FROM dmbdemo_agentsqa.questions
						WHERE query <> '' AND url <> ''
					) a ON binary p.channel = binary a.channel order by p.handle,a.qa_id";
$arr = array();
foreach ($channels as $channel => $dbName) {
//    echo sprintf($query, $channel, $dbName)."<br/>";
    $arr['channel'] = $channel;
    $result = mysql_query(sprintf($query, $channel, $dbName));
    while ($row = mysql_fetch_row($result)) {
        $arr['engagments'] = $row['engagments'];
    }

//    echo mysql_insert_id() . '<br/>';
//    exit();
}
?>
