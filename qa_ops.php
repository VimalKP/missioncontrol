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
mysql_select_db(DB_AgentQA_DATABASE) or die("Unable to select database");
//include_once 'includes/functions.php';

extract($_POST);

if ($action != '' && $action == "addEmailTo") {
    $res = mysql_query("UPDATE `contact` SET `send_mail_to`='$email' WHERE `z_contactid_pk`='$contactId'");
    echo $res;
} else if ($action != '' && $action == "getEmailTo") {
    $res = mysql_query("SELECT `send_mail_to` FROM `contact` WHERE `z_contactid_pk`='$contactId'");
    if ($result = mysql_fetch_assoc($res)) {
        echo $result['send_mail_to'];
    }
} else if ($action != '' && $action == "fetchqaresult_history") {
//    $res = mysql_query("SELECT l.checked_date,l.channel,l.score,l.status,c.username,s.testsize,s.passrate FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk join setting as s on s.channel=l.channel WHERE l.z_contactid_fk='$contactId'");
//    $res = mysql_query("SELECT max(l.checked_date) as checked_date,l.channel,l.score,l.status,c.username,s.testsize,s.passrate FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk join setting as s on s.channel=l.channel  WHERE l.z_contactid_fk='$contactId' group by l.channel ");
//    
    $res = mysql_query("(
SELECT max(l.checked_date) as checked_date,l.channel,l.z_testid_pk,l.score,l.status,c.username,l.testsize,l.passrate,l.level FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk 
join setting as s on s.channel= l.channel
    WHERE  l.checked_date in
 (
    SELECT max(ll.checked_date ) FROM logtable as ll WHERE channel in 
     (
         select channel from setting 
     ) group by ll.channel
  )
    group by l.channel
)");
    $i = 0;
    $channels_icon = array(
        'facebook' => 'images/facebook.png',
        'Instagram' => 'images/instagram.png',
        'twitter' => 'images/twitter.png',
        'youtube' => 'images/you-tube.png'
    );
//   $kj1='images/'.$kj.'png'; 
    $assoc = array();
    while ($result = mysql_fetch_array($res)) {
        $channel = $result['channel'];
        $assoc[0][$i]['checked_date'] = date('h:i A F d,Y', strtotime($result['checked_date']));
        $assoc[0][$i]['channel'] = ucfirst($result['channel']);
        $assoc[0][$i]['img'] = "images/" . $channel . ".png";
        $assoc[0][$i]['score'] = $result['score'];
        $assoc[0][$i]['z_testid_pk'] = $result['z_testid_pk'];
        $assoc[0][$i]['status'] = ucfirst($result['status']);
        $assoc[0][$i]['username'] = $result['username'];
//        if ($result['level'] == 'users') {
//            $assoc[0][$i]['testsize'] = 1;
//        } else {
        $assoc[0][$i]['testsize'] = $result['testsize'];
//        }
        $assoc[0][$i]['passrate'] = $result['passrate'];
        $i++;
    }
    $res2 = mysql_query("SELECT `send_mail_to` FROM `contact` WHERE `z_contactid_pk`='$contactId'");
    if ($result2 = mysql_fetch_assoc($res2)) {
        $assoc[1]['email'] = $result2['send_mail_to'];
    }

//    $channel_list =array();
//    foreach ($channels as $channel => $img) {
//        $channel_list[ucfirst($channel)]= $channel_list[$img]);
////        echo ucfirst($channel) . '<br/>';
//    }
//    $info = array(
//        'channels' => $channels_icon,
//    );
//    );
//    $info['datatext'] = 'hii';
    echo json_encode($assoc);
//    echo 'hi';
} else if ($action != '' && $action == "fetchqapage") {
    $contactId = $_SESSION['qa_contact_id'];
    $assoc = array();
    $result = array();
//    $query = "SELECT DISTINCT(handle) from $channels[$brand].brands where brandStatus=0 ORDER BY RAND() ";
//    $result = select($query);
    $result = chk_existent($brand);
    $assoc['chunkarray'] = $result;

    $brand_list = '';
    for ($i = 0; $i < count($result); $i++) {
        $brand_list.="<option value=" . $result[$i] . ">" . $result[$i] . "</option>";
//          $assoc['branddetail']['list'] = $result[$i]['handle'];
    }
    $brand_list.="<option value='randombrand'>Random Brand</option>";
    $brand_list.="<option value='randomuser'>Random User</option>";
    $assoc['branddetail']['list'] = $brand_list;
    $assoc['branddetail']['channel'] = ucfirst($brand);
    $assoc['branddetail']['img'] = "images/" . $brand . ".png";
//    $query2 = "SELECT count(*) as cnt,max(l.checked_date) as checked_date,l.channel,l.status,c.username FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk  WHERE l.z_contactid_fk='$contactId' AND  l.channel='$brand' group by l.channel";
//    $query2 = "SELECT count(*) as cnt,l.channel, l.status, c.username FROM `logtable` AS l JOIN contact AS c ON l.z_contactid_fk = c.z_contactid_pk where l.checked_date=(SELECT max(checked_date) FROM `logtable` WHERE channel = '$brand')";
    $query2 = "(SELECT count(*) as cnt,l.channel, l.status, c.username FROM `logtable` AS l JOIN contact AS c ON l.z_contactid_fk = c.z_contactid_pk where l.checked_date=(SELECT max(checked_date ) FROM `logtable` WHERE channel = '$brand')) UNION ALL (SELECT count(*) as cnt, NULL as channel, NULL as status,NULL as username FROM `logtable` AS l WHERE channel = '$brand')";
    $result2 = select($query2);
    if (count($result) > 0) {
        $assoc['branddetail']['lastchechked'] = $result2[0]['username'];
        $assoc['branddetail']['previous_status'] = ucfirst($result2[0]['status']);
        $assoc['branddetail']['total_checks'] = $result2[1]['cnt'];
    } else {
        $assoc['branddetail']['lastchechked'] = "-";
        $assoc['branddetail']['previous_status'] = "Not Yet Tested";
        $assoc['branddetail']['total_checks'] = "0";
    }
//    $result = mysql_query("SELECT DISTINCT(handle) from $channels[$brand]");
//    while ($opt = mysql_fetch_assoc($result)) {
//        $assoc['branddetail']['list'] = $opt['handle'];
//    }

    $assoc['branddetail']['query2'] = $query2;
    echo json_encode($assoc);
//    echo "$query";
} else if ($action != '' && $action == "fetchhistorydata") {
    $contactId = $_SESSION['qa_contact_id'];
    $assoc = array();
//    $query = "SELECT DISTINCT(handle) from $channels[$brand].brands";
//    $query = "SELECT l.z_testid_pk,l.checked_date,l.channel,l.score,l.level,l.status,c.username,l.testsize,l.passrate 
//FROM `logtable` as l  
// join contact as c on l.z_contactid_fk=c.z_contactid_pk 
// join setting as s on s.channel=l.channel  
//WHERE l.channel='$brand' and l.z_testid_pk in(
//select distinct(z_testid_fk) from answer
//    where z_testid_fk in (select z_testid_fk from answer
//                  group by z_testid_fk
//                  having count(z_testid_fk) > 1)
//order by z_testid_fk
//)
//ORDER  BY l.checked_date DESC";
    $query = "SELECT l.z_testid_pk,l.checked_date,l.channel,l.score,l.level,l.status,c.username,l.testsize,l.passrate FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk WHERE l.channel='$brand' ORDER  BY l.checked_date DESC";


//    $query = "SELECT l.z_testid_pk,l.checked_date,l.channel,l.score,l.status,c.username,s.testsize,s.passrate FROM `logtable` as l  join contact as c on l.z_contactid_fk=c.z_contactid_pk join setting as s on s.channel=l.channel  WHERE l.channel='$brand' ORDER  BY l.checked_date DESC ";
    $result = select($query);
//    $assoc['brandhistorydetail']['list']
    $history_table = '<center>  <table class="tablesorter" id="btable" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>Checked</th>
                                    <th>Varified By</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
';
    for ($i = 0; $i < count($result); $i++) {
        $channel = $result[$i]['channel'];
        if ($i % 2 == 0) {
            $trclass = "qa-even";
        } else {
            $trclass = "qa-odd";
        }
        if ($result[$i]['status'] == 'pass') {
            $status = "images/pass.png";
            $status_class = "green";
        } else if ($result[$i]['status'] == 'fail') {
            $status = "images/fail.png";
            $status_class = "red";
        } else {
            $status = "images/varification.png";
            $status_class = "dark-yellow";
        }

//        if ($result[$i]['level'] == 'users') {
//            $testsize = 1;
//        } else {
        $testsize = $result[$i]['testsize'];
//        }
        $history_table.='<tr class="' . $trclass . '">
                                    <td>' . date('h:i A F d,Y', strtotime($result[$i]['checked_date'])) . '</td>
                                    <td>' . $result[$i]['username'] . '</td>
                                    <td class="' . $status_class . ' large-font">' . $result[$i]['score'] . '/' . $testsize . '</td>
                                    <td class="green"><span style="cursor:pointer;" onclick="fetchresultpage(' . $result[$i]['z_testid_pk'] . ')"><img src="' . $status . '" alt="" /></span></td>
                                </tr>';
    }
    $history_table.='</tbody></table></center>';
    $assoc['brandhistorydetail']['list'] = $history_table;
    echo json_encode($assoc);
} else if ($action != '' && $action == "fetch_individualbrandqa_page" && $brandname != '' && $channel_name != '') {
    $assoc = array();

    $assoc['handle'] = $brandname;
    $assoc['channel'] = $channel_name;
    $query = "SELECT testsize,passrate from setting WHERE channel='$channel_name'";
    $result = select($query);
    $tablewise_samplesize = "SELECT * from tablewise_samplesize order BY  tablesampleSize_id";
    $tablewise_samplesize_array = select($tablewise_samplesize);
    for ($i = 0; $i < count($tablewise_samplesize_array); $i++) {
        $type = trim($tablewise_samplesize_array[$i]['questionType']);
        $sampleSize = trim($tablewise_samplesize_array[$i]['sampleSize']);
        $passrate = trim($tablewise_samplesize_array[$i]['passrate']);
        if ($type == 'brands') {
            $brandssampleSize = $sampleSize;
            $assoc['brandssampleSize'] = $brandssampleSize;
            $brandspassrate = $passrate;
        } else if ($type == 'posts') {
            $postssampleSize = $sampleSize;
            $assoc['postssampleSize'] = $postssampleSize;
            $postspassrate = $passrate;
        } else if ($type == 'engagements') {
            $engagementssampleSize = $sampleSize;
            $assoc['engagementssampleSize'] = $engagementssampleSize;
            $engagementspassrate = $passrate;
        } else if ($type == 'users') {
            $userssampleSize = $sampleSize;
            $assoc['userssampleSize'] = $userssampleSize;
            $userspassrate = $passrate;
        } else if ($type == 'userposts') {
            $userpostssampleSize = $sampleSize;
            $assoc['userpostssampleSize'] = $userpostssampleSize;
            $userpostspassrate = $passrate;
        } else if ($type == 'historical') {
            $historicalsampleSize = $sampleSize;
            $assoc['historicalsampleSize'] = $historicalsampleSize;
            $historicalpassrate = $passrate;
        }
    }



//    $randome_handle = "SELECT DISTINCT(handle) from $channels[$channel_name].brands WHERE brandStatus=ORDER BY RAND() LIMIT 1";

    $randome_handle_array = chk_existent($channel_name);

//    $randome_handle_array = select($randome_handle);
//    $random_handle = $randome_handle_array[0]['handle'];
    $random_handle = $randome_handle_array[array_rand($randome_handle_array)];


    $testsize = $result[0]['testsize'];
    $assoc['chunkarray'] = chk_existent($channel_name);
    $assoc['testsize'] = $testsize;
    $assoc['passrate'] = $result[0]['passrate'];


    $assoc['randomhandle'] = '';
    $assoc['userhandle'] = '';
    if ($brandname != 'randomuser') {
        if ($brandname == 'randombrand') {
            $assoc['randomhandle'] = $random_handle;
            $assoc['handle'] = $random_handle;
        } else {
            $assoc['handle'] = $brandname;
//            $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.passed,q.sampleSize,q.parent_qa_id,q.parent_passrate,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' order by  rand() limit $testsize";
        }
        $searchhandle = $assoc['handle'];


        if ($channel_name == 'instagram') {
            $fetchrandom_postID = "select postID,content from $channels[$channel_name].posts  where handle= '$searchhandle' AND likes>0 AND comments>0 ORDER BY rand() LIMIT $postssampleSize";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
            $pid = $fetchrandom_postIDarray[0][0];
            $fetchrandom_postIDarray[0][2] = "http://web.stagram.com/p/$pid";
            $fetchrandom_postIDarray[0]['link'] = "http://web.stagram.com/p/$pid";
            $assoc['postIdarray'] = $fetchrandom_postIDarray;
        } else if ($channel_name == 'twitter') {
            $fetchrandom_postID = "select postID,handle from $channels[$channel_name].posts  where handle= '$searchhandle' AND content!=''  ORDER BY rand() LIMIT $postssampleSize";
//            $fetchrandom_postID='dfdsf';
            //  $fetchrandom_postID="select up.postID,up.content,up.link,u.handle from $channels[$channel_name].posts  as up join $channels[$channel_name].users as u ON up.userID=u.userID where up.handle= '$searchhandle' AND up.content!=''  ORDER BY rand() LIMIT 1";
//                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);
//                $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
//            $fetchrandom_postID = "select postID,content,link from $channels[$channel_name].posts  where handle= '$searchhandle'  AND likes>0 AND comments>0  ORDER BY rand() LIMIT 1";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
            $assoc['postIdarray'] = $fetchrandom_postIDarray;
            $assoc['fetchrandom_postID'] = $fetchrandom_postID;
            $assoc['fetchrandom_postID1'] = '23';
        } else {
            $fetchrandom_postID = "select postID,content,link from $channels[$channel_name].posts  where handle= '$searchhandle'  AND likes>0 AND comments>0  ORDER BY rand() LIMIT $postssampleSize";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
            $assoc['postIdarray'] = $fetchrandom_postIDarray;
        }
        if ($channel_name != 'instagram') {
            if ($channel_name == 'twitter') {
                $fetchrandomuser_postID = "select up.postID,up.content,up.link,u.handle from $channels[$channel_name].userposts  as up join $channels[$channel_name].users as u ON up.userID=u.userID where up.handle= '$searchhandle' AND up.content!=''  ORDER BY rand() LIMIT $userpostssampleSize";
                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);
                $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
            } else {
                $fetchrandomuser_postID = "select postID,content,link from $channels[$channel_name].userposts  where handle= '$searchhandle' AND content!=''  ORDER BY rand() LIMIT $userpostssampleSize";
                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);
                $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
            }

//            if()
        }
        if ($channel_name == 'twitter') {
            $fetchrandom_engagementID = "select e.engagementID,u.handle from $channels[$channel_name].engagements as e join $channels[$channel_name].users as u ON e.userID=u.userID where e.handle= '$searchhandle' AND e.content!='' AND e.engagementID!='' ORDER BY rand() LIMIT $engagementssampleSize";
            $fetchrandom_engagementIDarray = select($fetchrandom_engagementID);
            $assoc['engagementIdarray'] = $fetchrandom_engagementIDarray;
        } else if ($channel_name == 'facebook') {
            $fetchrandom_engagementID = "select distinct(postID), engagementID from $channels[$channel_name].engagements where handle= '$searchhandle' AND content!='' AND engagementType='comment' AND engagementID!='' ORDER BY rand() LIMIT $engagementssampleSize";
            $fetchrandom_engagementIDarray = select($fetchrandom_engagementID);
            $assoc['engagementIdarray'] = $fetchrandom_engagementIDarray;
        }


        $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' AND q.questionType!='users' order by  rand() limit $testsize";
    } else if ($brandname == 'randomuser') {

        $randuserquery = "SELECT friendly,handle,userID FROM $channels[$channel_name]" . ".users WHERE friendly != '' AND handle != '' ORDER BY rand() LIMIT $userssampleSize ";
        $assoc['$randuserquery'] = $randuserquery;
        $randuserquery_result = select($randuserquery);
        $assoc['randomeusername'] = $randuserquery_result[0]['friendly'];
        $assoc['userhandle'] = $randuserquery_result[0]['handle'];
        $assoc['userID'] = $randuserquery_result[0]['userID'];
        $assoc['userIdarray'] = $randuserquery_result;
        $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' AND q.questionType='users' order by  rand() limit $testsize";
    }


    $queArr = select($query);
    $assoc['quesize'] = count($queArr);
    $mainQueSize = count($queArr);
    $allQue = array();
    $pagenation = "<ul><li><a onclick='getPageData(1)' href='javascript:void(0);'> &lt;&lt; </a></li>
            <li><a onclick='getPageData()' id='prevPage' href='javascript:void(0);'> &lt; </a></li>";
    foreach ($queArr as $value) {
        $allQue[] = $value;
//        if ($value['sampleSize'] > 1) {
//            $lt = $value['st'] - 1;
//            $q = "SELECT * FROM `questions`    WHERE `channel` = '$channel_name' and `parent_qa_id`=" . $value['qa_id'] . " and `query`!='' LIMIT $lt";
//            $subqueArr = select($q);
//            foreach ($subqueArr as $subvalue) {
//                $allQue[] = $subvalue;
//            }
//        }
//        $assoc['question'][] = $value;
    }
    for ($i = 0; $i < count($queArr); $i++) {
        $next = $i + 1;
        $qt = $queArr[$i]['questionType'];
        $pagenation.="<li><a onclick='getPageData($next)' id='page_$next' href='javascript:void(0);' class='pages $qt'>$next</a></li>";
    }
    $pagenation.="<li><a onclick='' id='nextPage' href='javascript:void(0);'> &gt; </a></li>
            <li><a onclick='getPageData($mainQueSize)' href='javascript:void(0);'> &gt;&gt; </a></li></ul>";
    $assoc['allquesize'] = count($allQue);
    $assoc['question'] = $allQue;
    $assoc['pagination'] = $pagenation;



    echo json_encode($assoc);
} else if ($action != '' && $action == "fetchresultpage") {
    $qetype = "select * from answer as a join logtable as l ON l.z_testid_pk=a.z_testid_fk  WHERE  l.z_testid_pk =$z_testid_fk LIMIT 1";
    $qetypearray = select($qetype);
    $brand_name = $qetypearray[0]['handle'];
    $leavel = trim($qetypearray[0]['level']);

    $query = "SELECT a.`qa_id` , a.`z_testid_fk` , a.`expected` , a.`input` , a.`passed` ,a.handle,q.`channel`, q.`question`  , q.`parent_qa_id` 
            FROM answer a
            INNER JOIN questions q ON ( a.qa_id = q.qa_id ) 
            WHERE a.`z_testid_fk` =  '$z_testid_fk'";
    $result = select($query);
    $assoc = array();
    $assoc['result_query'] = '';
    if (count($result) > 0) {
        for ($i = 0; $i < count($result); $i++) {
            $assoc['result_query'][$i]['qa_id'] = $result[$i]['qa_id'];
            $assoc['result_query'][$i]['z_testid_fk'] = $result[$i]['z_testid_fk'];
            $assoc['result_query'][$i]['expected'] = $result[$i]['expected'];
            $assoc['result_query'][$i]['input'] = $result[$i]['input'];
            $assoc['result_query'][$i]['passed'] = $result[$i]['passed'];
            $assoc['result_query'][$i]['question'] = $result[$i]['question'];
//            $assoc['result_query'][$i]['sampleSize'] = $result[$i]['sampleSize'];
//            $assoc['result_query'][$i]['parent_qa_id'] = $result[$i]['parent_qa_id'];
//            $assoc['result_query'][$i]['parent_passrate'] = $result[$i]['parent_passrate'];
        }
        $assoc['result_query'][0]['brand_name'] = $result[0]['handle'];
        $assoc['result_query'][0]['channel_name'] = $result[0]['channel'];
    } else {
        $assoc['error'] = 'yes';
    }
    $mainhandle = $result[0]['handle'];
    $summary = "select c.username,a.handle, l.testsize,l.checked_date,l.score,l.status from  logtable as l  join setting as s ON s.channel=l.channel join contact c on c.z_contactid_pk=l.z_contactid_fk join answer as a on  a.z_testid_fk=l.z_testid_pk WHERE l.z_testid_pk='$z_testid_fk' AND a.handle='$mainhandle' LIMIT 1";
    $summaryarray = select("$summary");
    $assoc['username'] = $summaryarray[0]['username'];
    $assoc['handle'] = $summaryarray[0]['handle'];
    $assoc['leavel'] = $leavel;

//    if ($leavel == 'users') {
//        $assoc['testsize'] = 1;
//    } else {
    $assoc['testsize'] = $summaryarray[0]['testsize'];
//    }


    $assoc['checked_date'] = $summaryarray[0]['checked_date'];
    $assoc['score'] = $summaryarray[0]['score'];
    $assoc['status'] = $summaryarray[0]['status'];
    $finaljson = json_encode($assoc);
    echo $finaljson;
} else if ($action != '' && $action == "editresultpage") {
    $assoc = array();
//    $leavel=
//    if ($leavel == 'brands') {
    $qetype = "select * from answer as a join logtable as l ON l.z_testid_pk=a.z_testid_fk  WHERE  l.z_testid_pk =$z_testid_fk LIMIT 1";
    $qetypearray = select($qetype);
    $brandname = $qetypearray[0]['handle'];
    $leavel = $qetypearray[0]['level'];
    $InserteduserID = $qetypearray[0]['postID'];
    $query = "SELECT * FROM questions WHERE qa_id IN (
                    SELECT DISTINCT (`qa_id`)
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_fk AND  `handle` =  '" . $brandname . "' order by ans_id
		)
        AND channel =  '" . $channel_name . "'
        AND query !=  ''
        AND parent_qa_id =0";
    $parent_answered_que = select($query);
    $assoc['$parent_answered_que'] = $parent_answered_que;

//    $no_of_ansed_que = count($parent_answered_que);

    $q = "SELECT count(*) as cnt,a.qa_id,q.questionType FROM answer as a JOIN questions as q ON a.qa_id=q.qa_id WHERE a.z_testid_fk=$z_testid_fk AND a.handle =  '" . $brandname . "' group by a.qa_id order by a.ans_id";
    $arr = select($q);
    $answerd_qaid = array();
    $answerd_qaid_quetype = array();

//    $answered_postIds=array();
    if (isset($arr) && $arr != array()) {
        foreach ($arr as $value) {
            $answerd_qaid[] = $value['qa_id'];
//            $answerd_qaid[] = $value['questionType'];
//            $answered_postIds[$value['questionType']]=$value['']
        }
    }
    if (isset($arr) && $arr != array()) {
        for ($i = 0; $i < count($arr); $i++) {
            $answerd_qaid_quetype[$i]['qa_id'] = $arr[$i]['qa_id'];
            $answerd_qaid_quetype[$i]['questionType'] = $arr[$i]['questionType'];
//            $answered_postIds[$value['questionType']]=$value['']
        }
    }
    $assoc['$answerd_qaid_quetype'] = $answerd_qaid_quetype;
    $assoc['answerd_qaid'] = $arr;
    $no_of_ansed_que = count($arr);
    $q = "SELECT distinct a.postID,q.questionType FROM answer as a JOIN questions as q ON a.qa_id=q.qa_id WHERE a.z_testid_fk=$z_testid_fk AND a.handle =  '" . $brandname . "'";
    $allAnsweredQue = select($q);

    if (isset($allAnsweredQue) && $allAnsweredQue != array()) {
        foreach ($allAnsweredQue as $value) {
            if ($value['postID'] != '')
                $answerd_postIds[trim($value['questionType'])][] = $value['postID'];
//            $answered_postIds[$value['questionType']]=$value['']
        }
    }

    $assoc['handle'] = $brandname;
    $assoc['channel'] = $channel_name;
    $query = "SELECT * from logtable WHERE z_testid_pk='$z_testid_fk'";
    $result = select($query);

    $testsize = $result[0]['testsize'];
    $assoc['testsize'] = $testsize;
    $testsize_remain = intval($testsize - $no_of_ansed_que);

    $tablewise_samplesize = "SELECT * from tablewise_samplesize order BY  tablesampleSize_id";
    $tablewise_samplesize_array = select($tablewise_samplesize);
    for ($i = 0; $i < count($tablewise_samplesize_array); $i++) {
        $type = trim($tablewise_samplesize_array[$i]['questionType']);
        $sampleSize = trim($tablewise_samplesize_array[$i]['sampleSize']);
        $passrate = trim($tablewise_samplesize_array[$i]['passrate']);

        if ($type == 'brands') {
            $brandssampleSize = $sampleSize;
            $assoc['brandssampleSize'] = $brandssampleSize;

            if ((isset($answerd_postIds['brands'])) && (count($answerd_postIds['brands']) <= $sampleSize)) {
                $brandssampleSize = intval($sampleSize) - count($answerd_postIds['brands']);
            }

            $brandspassrate = $passrate;
        } else if ($type == 'posts') {
            $postssampleSize = $sampleSize;
            $assoc['postssampleSize'] = $postssampleSize;

            if ((isset($answerd_postIds['posts'])) && (count($answerd_postIds['posts']) <= $sampleSize)) {
                $postssampleSize = intval($sampleSize) - count($answerd_postIds['posts']);
            }

            $postspassrate = $passrate;
        } else if ($type == 'engagements') {
            $engagementssampleSize = $sampleSize;
            $assoc['engagementssampleSize'] = $engagementssampleSize;

            if ((isset($answerd_postIds[$type])) && (count($answerd_postIds[$type]) <= $sampleSize)) {
                $engagementssampleSize = intval($sampleSize) - count($answerd_postIds[$type]);
            }

            $engagementspassrate = $passrate;
        } else if ($type == 'users') {
            $userssampleSize = $sampleSize;
            $assoc['userssampleSize'] = $userssampleSize;
//
//            if ((isset($answerd_postIds['users'])) && (count($answerd_postIds['users']) <= $sampleSize)) {
//                $userssampleSize = intval($sampleSize) - count($answerd_postIds['users']);
//            }

            $userspassrate = $passrate;
        } else if ($type == 'userposts') {
            $userpostssampleSize = $sampleSize;
            $assoc['userpostssampleSize'] = $userpostssampleSize;

            if ((isset($answerd_postIds[$type])) && (count($answerd_postIds[$type]) <= $sampleSize)) {
                $userpostssampleSize = intval($sampleSize) - count($answerd_postIds[$type]);
            }

            $userpostspassrate = $passrate;
        } else if ($type == 'historical') {

            $historicalsampleSize = $sampleSize;
            $assoc['historicalsampleSize'] = $historicalsampleSize;

            if ((isset($answerd_postIds[$type])) && (count($answerd_postIds[$type]) <= $sampleSize)) {
                $historicalsampleSize = intval($sampleSize) - count($answerd_postIds[$type]);
            }
            $historicalpassrate = $passrate;
        }
    }

//    $randome_handle = "SELECT DISTINCT(handle) from $channels[$channel_name].brands WHERE brandStatus=ORDER BY RAND() LIMIT 1";
    $randome_handle_array = chk_existent($channel_name);

//    $randome_handle_array = select($randome_handle);
//    $random_handle = $randome_handle_array[0]['handle'];
    $random_handle = $randome_handle_array[array_rand($randome_handle_array)];


    $assoc['chunkarray'] = chk_existent($channel_name);

    $assoc['passrate'] = $result[0]['passrate'];

    $assoc['randomhandle'] = '';
    $assoc['userhandle'] = '';
    if ($leavel != 'users') {
//        if ($brandname == 'randombrand') {
//            $assoc['randomhandle'] = $random_handle;
//            $assoc['handle'] = $random_handle;
//        } else {
        $assoc['handle'] = $brandname;
//            $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.passed,q.sampleSize,q.parent_qa_id,q.parent_passrate,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' order by  rand() limit $testsize";
//        }
        $searchhandle = $assoc['handle'];


        $assoc['postIdarray'] = array();
        //////  to make post id array for which posts' answer given. :: start

        if ($answerd_postIds['posts'] && $answerd_postIds['posts'] != array()) {
            for ($i = 0; $i < count($answerd_postIds['posts']); $i++) {
//                $assoc['postIdarray'][][0] = $answerd_postIds['posts'][$i];
                $assoc['postIdarray'][]['postID'] = $answerd_postIds['posts'][$i];
            }
        }
        //////  to make post id array for which posts' answer given. :: end

        if ($channel_name == 'instagram') {
            $fetchrandom_postID = "select postID,content from $channels[$channel_name].posts  where handle= '$searchhandle' AND likes>0 AND comments>0 ORDER BY rand() LIMIT $postssampleSize";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
//            $pid = $fetchrandom_postIDarray[0][0];
//            array_push($answerd_postIds['posts'], $pid);
//            $fetchrandom_postIDarray[0][2] = "http://web.stagram.com/p/$pid";
//            $fetchrandom_postIDarray[0]['link'] = "http://web.stagram.com/p/$pid";
            if ($fetchrandom_postIDarray != array())
//                array_push($assoc['postIdarray'], $fetchrandom_postIDarray);
                for ($i = 0; $i < count($fetchrandom_postIDarray); $i++) {
                    array_push($assoc['postIdarray'], $fetchrandom_postIDarray[$i]);
                }
//            $assoc['postIdarray'] = $fetchrandom_postIDarray;
        } else if ($channel_name == 'twitter') {
            $fetchrandom_postID = "select postID,handle from $channels[$channel_name].posts  where handle= '$searchhandle' AND content!=''  ORDER BY rand() LIMIT $postssampleSize";
//            $fetchrandom_postID='dfdsf';
            //  $fetchrandom_postID="select up.postID,up.content,up.link,u.handle from $channels[$channel_name].posts  as up join $channels[$channel_name].users as u ON up.userID=u.userID where up.handle= '$searchhandle' AND up.content!=''  ORDER BY rand() LIMIT 1";
//                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);
//                $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
//            $fetchrandom_postID = "select postID,content,link from $channels[$channel_name].posts  where handle= '$searchhandle'  AND likes>0 AND comments>0  ORDER BY rand() LIMIT 1";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
            if ($fetchrandom_postIDarray != array())
                for ($i = 0; $i < count($fetchrandom_postIDarray); $i++) {
                    array_push($assoc['postIdarray'], $fetchrandom_postIDarray[$i]);
                }
//            $assoc['postIdarray'] = $fetchrandom_postIDarray;
            $assoc['fetchrandom_postID'] = $fetchrandom_postID;
            $assoc['fetchrandom_postID1'] = '23';
        } else {
            $fetchrandom_postID = "select postID,content,link from $channels[$channel_name].posts  where handle= '$searchhandle'  AND likes>0 AND comments>0  ORDER BY rand() LIMIT $postssampleSize";
            $fetchrandom_postIDarray = select($fetchrandom_postID);
            if ($fetchrandom_postIDarray != array())
                for ($i = 0; $i < count($fetchrandom_postIDarray); $i++) {
                    array_push($assoc['postIdarray'], $fetchrandom_postIDarray[$i]);
                }

//            $assoc['postIdarray'] = $fetchrandom_postIDarray;
        }
        $assoc['postsamplesize'] = $postssampleSize;
        $assoc['userpostIdarray'] = array();
        if ($answerd_postIds['userposts'] && $answerd_postIds['userposts'] != array()) {
            for ($i = 0; $i < count($answerd_postIds['userposts']); $i++) {
                $assoc['userpostIdarray'][]['postID'] = $answerd_postIds['userposts'][$i];
            }
        }

        if ($channel_name != 'instagram') {
            if ($channel_name == 'twitter') {
                $fetchrandomuser_postID = "select up.postID,up.content,up.link,u.handle from $channels[$channel_name].userposts  as up join $channels[$channel_name].users as u ON up.userID=u.userID where up.handle= '$searchhandle' AND up.content!=''  ORDER BY rand() LIMIT $userpostssampleSize";
                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);
                if ($fetchrandomuser_postIDarray != array())
//                    array_push($assoc['userpostIdarray'], $fetchrandomuser_postIDarray);
//                    $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
                    for ($i = 0; $i < count($fetchrandomuser_postIDarray); $i++) {
                        array_push($assoc['userpostIdarray'], $fetchrandomuser_postIDarray[$i]);
                    }
            } else {
                $fetchrandomuser_postID = "select postID,content,link from $channels[$channel_name].userposts  where handle= '$searchhandle' AND content!=''  ORDER BY rand() LIMIT $userpostssampleSize";
                $fetchrandomuser_postIDarray = select($fetchrandomuser_postID);

                if ($fetchrandomuser_postIDarray != array() && $fetchrandomuser_postIDarray != null)
//                    array_push($assoc['userpostIdarray'], $fetchrandomuser_postIDarray);
//                    $assoc['userpostIdarray'] = $fetchrandomuser_postIDarray;
                    for ($i = 0; $i < count($fetchrandomuser_postIDarray); $i++) {
                        array_push($assoc['userpostIdarray'], $fetchrandomuser_postIDarray[$i]);
                    }
            }
        }

        //////  to make post id array for which userposts' answer given. :: start
        //////  to make post id array for which userposts' answer given. :: end


        $assoc['engagementIdarray'] = array();
        //////  to make post id array for which engagements' answer given. :: start

        if ($answerd_postIds['engagements'] && $answerd_postIds['engagements'] != array()) {
            for ($i = 0; $i < count($answerd_postIds['engagements']); $i++) {
                $assoc['engagementIdarray'][]['engagementID'] = $answerd_postIds['engagements'][$i];
            }
        }
        //////  to make post id array for which engagements' answer given. :: end

        if ($channel_name == 'twitter') {
            $fetchrandom_engagementID = "select e.engagementID,u.handle from $channels[$channel_name].engagements as e join $channels[$channel_name].users as u ON e.userID=u.userID where e.handle= '$searchhandle' AND e.content!='' ORDER BY rand() LIMIT $engagementssampleSize";
            $fetchrandom_engagementIDarray = select($fetchrandom_engagementID);

            if ($fetchrandom_engagementIDarray != array())
                for ($i = 0; $i < count($fetchrandom_engagementIDarray); $i++) {
                    array_push($assoc['engagementIdarray'], $fetchrandom_engagementIDarray[$i]);
                }
        }
        if ($channel_name == 'facebook') {
            $fetchrandom_engagementID = "select distinct(postID), engagementID from $channels[$channel_name].engagements where handle= '$searchhandle' AND content!='' AND engagementType='comment' ORDER BY rand() LIMIT $engagementssampleSize";
            $fetchrandom_engagementIDarray = select($fetchrandom_engagementID);
            if ($fetchrandom_engagementIDarray != array())
                for ($i = 0; $i < count($fetchrandom_engagementIDarray); $i++) {
                    array_push($assoc['engagementIdarray'], $fetchrandom_engagementIDarray[$i]);
                }
        }
        $assoc['testsize_remain'] = $testsize_remain;
        $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' AND q.questionType!='users' AND q.qa_id NOT IN(" . implode(',', $answerd_qaid) . ") order by  rand() limit $testsize_remain";
        $query2 = "SELECT DISTINCT(q.qa_id),q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType JOIN answer as a on a.qa_id=q.qa_id WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' and q.qa_id IN (" . implode(',', $answerd_qaid) . ") AND q.questionType!='users' AND z_testid_fk='$z_testid_fk'  order by a.ans_id";
    } else if ($leavel == 'users') {
        $q = "SELECT DISTINCT postID, handle FROM answer WHERE z_testid_fk =$z_testid_fk";
        $allAnsweredQue = select($q);
        $newarr = array();
        for ($j = 0; $j < count($allAnsweredQue); $j++) {
            $newarr[$j]['userID'] = $allAnsweredQue[$j]['postID'];
            $newarr[$j]['handle'] = $allAnsweredQue[$j]['handle'];
        }
        $userssampleSize = $userssampleSize - count($allAnsweredQue);
        $assoc['$userssampleSizeremain'] = $userssampleSize;
//        $userIdarray = array();
        $assoc['userIdarray'] = array();
//        if ($answerd_postIds['users'] && $answerd_postIds['users'] != array()) {
//            for ($i = 0; $i < count($answerd_postIds['users']); $i++) {
//                $assoc['userIdarray'][]['userID'] = $answerd_postIds['users'][$i];
//            }
//        }
//
        $randuserquery = "SELECT handle,userID FROM $channels[$channel_name]" . ".users WHERE friendly != '' AND handle != '' AND userID!='$InserteduserID' ORDER BY rand() LIMIT $userssampleSize ";
        $assoc['$randuserquery'] = $randuserquery;
        $randuserquery_result = select($randuserquery);

//        $userIdarray[0][0] = $brandname;
//        $userIdarray[0][1] = $InserteduserID;
//        $userIdarray[0]['handle'] = $brandname;
//        $userIdarray[0]['userID'] = $InserteduserID;
        if ($randuserquery_result != array())
            for ($i = 0; $i < count($randuserquery_result); $i++) {
                array_push($newarr, $randuserquery_result[$i]);
            }
//        $assoc['randomeusername'] = $randuserquery_result[0]['friendly'];
        $assoc['userhandle'] = $newarr[0]['handle'];
        $assoc['$userssampleSize'] = $userssampleSize;
        $assoc['userID'] = $newarr[0]['userID'];
//        $queArr = array_merge($userIdarray, $randuserquery_result);
        $assoc['userIdarray'] = $newarr;
        $assoc['userhandle'] = $brandname;
        $assoc['userID'] = $InserteduserID;
        $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' AND q.questionType='users' AND q.qa_id NOT IN(" . implode(',', $answerd_qaid) . ") order by  rand() limit $testsize_remain";
        $query2 = "SELECT DISTINCT(q.qa_id),q.channel,q.question,q.query,q.url,q.tolerance,q.field,q.parent_qa_id,q.questionType,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType  JOIN answer as a on a.qa_id=q.qa_id WHERE q.channel='$channel_name' and q.parent_qa_id=0 and q.query!='' AND q.questionType='users' AND q.qa_id IN(" . implode(',', $answerd_qaid) . ") AND z_testid_fk='$z_testid_fk'  order by a.ans_id";
    }

    $queArr = select($query);   //new non answered question random array.
    $queArr1 = select($query2); //answered question's array.
    $assoc['$queArr'] = $queArr;
    $assoc['$queArr11'] = $queArr1;
    $queArr = array_merge($queArr1, $queArr); //join both question
//    $queArr = array_merge($queArr, $queArr1); //join both question

    $assoc['quesize'] = count($queArr);
    $mainQueSize = count($queArr);
    $allQue = array();
    $pagenation = "<ul><li><a onclick='getPageData(1)' href='javascript:void(0);'> &lt;&lt; </a></li>
            <li><a onclick='getPageData()' id='prevPage' href='javascript:void(0);'> &lt; </a></li>";
    foreach ($queArr as $value) {
        $allQue[] = $value;
    }

    /*
     * fetch answer detail of answered question
     */
//    $qu = "SELECT * FROM `answer` WHERE `z_testid_fk` =$z_testid_fk AND `handle` = '" . $brandname . "' and qa_id IN (" . implode(',', $answerd_qaid) . ") order by qa_id";
//    $ans = select($qu);
//    $answers = array();
//
//    foreach ($ans as $value) {
//        $answers[$value['qa_id']]['ans'] = $value['input'];
//        $answers[$value['qa_id']]['passed'] = $value['passed'];
//    }
//    $assoc['answers'] = $answers;
    for ($i = 0; $i < count($answerd_qaid_quetype); $i++) {
        $questionType = trim($answerd_qaid_quetype[$i]['questionType']);
//        $parent_qa_id = $parent_answered_que[$i]['parent_qa_id'];
        $qa_id = $answerd_qaid_quetype[$i]['qa_id'];


        $class = '';
        $find_pre_qa = '';
        if ($questionType == 'brands' || $questionType == 'historical') {        ////condition of single questions
            $find_pre_qa = "SELECT passed from answer where z_testid_fk='$z_testid_fk' AND qa_id='" . $qa_id . "' AND handle='$brandname' limit 1";

            $outresult = select($find_pre_qa);
            $passed = $outresult[0][0];
            if ($passed == 1) {
                $class = 'pass_paginate';
            } else {
                $class = 'fail_paginate';
            }
        } else if ($questionType == 'posts') {
            $find_pre_qa = "SELECT count(*) as cnt from answer where z_testid_fk='$z_testid_fk' AND qa_id='" . $qa_id . "' AND handle='$brandname' AND passed='1'";

            $outresult = select($find_pre_qa);
            $passed = $outresult[0][0];
            $psrate = ceil($postspassrate * $assoc['postssampleSize']);
            $assoc['$psrate'][$i] = $psrate . '_' . $qa_id;
            $assoc['$outresult'][$i] = $outresult . '_' . $qa_id;
            if ($passed >= $psrate) {
                $class = 'pass_paginate';
            } else {
                $class = 'fail_paginate';
            }
        } else if ($questionType == 'userposts') {
            $find_pre_qa = "SELECT count(*) as cnt from answer where z_testid_fk='$z_testid_fk' AND qa_id='" . $qa_id . "' AND handle='$brandname' AND passed='1'";

            $outresult = select($find_pre_qa);
            $passed = $outresult[0][0];
            $psrate = ceil($userpostspassrate * $assoc['userpostssampleSize']);
//          $assoc['getresult_pass'] = $outresult;
            if ($passed >= $psrate) {
                $class = 'pass_paginate';
            } else {
                $class = 'fail_paginate';
            }
        } else if ($questionType == 'engagements') {
            $find_pre_qa = "SELECT count(*) as cnt from answer where z_testid_fk='$z_testid_fk' AND qa_id='" . $qa_id . "' AND handle='$brandname' AND passed='1'";

            $outresult = select($find_pre_qa);
            $passed = $outresult[0][0];
            $psrate = ceil($engagementspassrate * $assoc['engagementssampleSize']);
            if ($passed >= $psrate) {
                $class = 'pass_paginate';
            } else {
                $class = 'fail_paginate';
            }
        } else if ($questionType == 'users') {
            $find_pre_qa = "SELECT count(*) as cnt from answer where z_testid_fk='$z_testid_fk' AND qa_id='" . $qa_id . "' AND handle='$brandname' AND passed='1'";

            $outresult = select($find_pre_qa);
            $passed = $outresult[0][0];
            $psrate = ceil($userspassrate * $assoc['userssampleSize']);
            if ($passed >= $psrate) {
                $class = 'pass_paginate';
            } else {
                $class = 'fail_paginate';
            }
        }



//        if ($ans[$i]['passed'] == -1) {
//            $class = 'fail_paginate';
//        } else if ($ans[$i]['passed'] == 1) {
//            $class = 'pass_paginate';
//        }
        $next = $i + 1;
        $pagenation.="<li><a onclick='getPageData($next)' class='pages $questionType $class' id='page_$next' href='javascript:void(0);' class='pages'>$next</a></li>";
    }

    for ($i = $no_of_ansed_que; $i < count($queArr); $i++) {
        $next = $i + 1;
        $questionType = $queArr[$i]['questionType'];
        $pagenation.="<li><a onclick='getPageData($next)' id='page_$next' href='javascript:void(0);' class='pages $questionType'>$next</a></li>";
    }

    $pagenation.="<li><a onclick='' id='nextPage' href='javascript:void(0);'> &gt; </a></li>
            <li><a onclick='getPageData($mainQueSize)' href='javascript:void(0);'> &gt;&gt; </a></li></ul>";
    $assoc['allquesize'] = count($allQue);
    $assoc['question'] = $allQue;
    $assoc['pagination'] = $pagenation;

    echo json_encode($assoc);
} else if ($action != '' && $action == "check_score") {
    $rightCnt = 0;
    $maincount = 0;
    $assoc = array();
//    $leavel
    // to find count of main que. which is passed.
    $query = "SELECT count(*) FROM questions WHERE qa_id IN (
                    SELECT DISTINCT (`qa_id`)
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_fk AND `handle` =  '" . $brand_name . "' AND passed=1 order by qa_id
		)
        AND channel =  '" . $channel_name . "'
        AND query !=  ''
        AND parent_qa_id = 0 and sampleSize=1";
    $no_parent_passed_que = select($query);

    $rightCnt = $no_parent_passed_que[0][0];
    $query = "SELECT q.qa_id,q.channel,q.question,q.query,q.url,q.tolerance,q.passed,q.sampleSize,q.parent_qa_id,q.parent_passrate,t.sampleSize as st,t.passrate as tt 
        FROM questions as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.qa_id IN (
                    SELECT DISTINCT (`qa_id`)
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_fk AND `handle` =  '" . $brand_name . "' order by qa_id
		)
        AND q.channel =  '" . $channel_name . "'
        AND q.query !=  ''
        AND q.parent_qa_id = 0 and q.sampleSize>1";
    $parent_answered_que = select($query);
    $no_of_main_que = count($parent_answered_que);
    $ansed_ids = array();
    if (is_array($parent_answered_que) || isset($parent_answered_que)) {
        foreach ($parent_answered_que as $value) {
            $assoc['inif'] = 'inis';
            $ansed_ids[] = $value['qa_id'];

            $assoc['mainqa_id'] = $value['qa_id'];
            $qu1 = "SELECT count(*) as cnt
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_fk AND `handle` =  '" . $brand_name . "' and  qa_id =" . $value['qa_id'] . " AND passed=1";
            $mainpass = select($qu1);
            if ($mainpass[0]['cnt'] > 0) {
                $maincount++;
            }
            $lt = $value['st'] - 1;
            $assoc['limit'] = $lt;
//            if ($value['sampleSize'] > 1) {
            $q = "SELECT qa_id FROM `questions` WHERE `channel` = '$channel_name' and `parent_qa_id`=" . $value['qa_id'] . " and `query`!=''  ORDER BY qa_id LIMIT $lt";
            $subqueArr = select($q);
            $allsubQueIds = array();
            foreach ($subqueArr as $subvalue) {
                $allsubQueIds[] = $subvalue['qa_id'];
            }
            $allsubQueIds = implode(',', $allsubQueIds);
            $qu = "SELECT count(*) as cnt
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_fk AND `handle` =  '" . $brand_name . "' and  qa_id IN($allsubQueIds) AND passed=1";
            $cnt_passed = select($qu);
            $sampleSize = $value['sampleSize'];
            $tt = $value['tt'];
            $st = $value['st'];
            if ($sampleSize > $st) {
                $passrate = ceil($st * $tt);
            } else {
                $passrate = ceil($sampleSize * $tt);
            }
            $assoc['passrate'] = $passrate;
            $assoc['maincount'] = $maincount;
            $assoc['subcount'] = $cnt_passed[0]['cnt'];
            $final = $cnt_passed[0]['cnt'] + $maincount;
            if ($passrate <= $final) {
                $rightCnt++;
                if ($leavel == 'users') {
                    $assoc['users'] = 'users';
                    $q = "UPDATE `logtable` SET score='1',status='pass'  WHERE  `z_testid_pk`='$z_testid_fk'";
                    $assoc['$q'] = $q;
                    mysql_query($q);
                }
//                $q = "UPDATE `answer` SET passed=1 WHERE `qa_id`=" . $value['qa_id'] . " and `z_testid_fk`='$z_testid_fk'";
//                mysql_query($q);
            } else {
//                $q = "UPDATE `answer` SET passed=-1 WHERE `qa_id`=" . $value['qa_id'] . " and `z_testid_fk`='$z_testid_fk'";
//                mysql_query($q);
            }

//            }
//        $assoc['question'][] = $value;
        }
    } else {
        $assoc['inelse'] = 'inelse';
    }

    if ($leavel == 'brands') {
        $settingQue = "SELECT passrate,testsize
	FROM setting
	WHERE channel='" . $channel_name . "'";

        $total_passrate = select($settingQue);
        $totalRate = ceil($total_passrate[0][0] * $total_passrate[0][1]);
        if ($totalRate <= $rightCnt) {
            $status = "pass";
        } else {
            $status = 'fail';
        }
        $q = "UPDATE `logtable` SET score=$rightCnt ,status='$status' WHERE `z_testid_pk`='$z_testid_fk'";
        mysql_query($q);
    }


    echo json_encode($assoc);
} else if ($action != '' && $action == "send_mail") {
//    Mail setup ::: start

    $mails = "SELECT send_mail_to FROM contact WHERE z_contactid_pk =  '$contact_id'";
    $mailArr = select($mails);
//    $mailArr=  explode(",", $mailArr[0][0]);
    $subject = "Result Of QA test";
    $mailbody = "";
    $mailbody = $mailbody . "<html><title> Result </title><body>";
    $mailbody = $mailbody . "<p>Hello,</p>";
    $mailbody = $mailbody . "<pre>" . $msg_body . "</pre>";
    $mailbody = $mailbody . "<p>The request is submitted on <b>" . date("Y-m-d H:i:s") . "</b></p>";
    $mailbody = $mailbody . "<p>Thanks,</p>" . "<p>Digital Menubox</p>";
    $mailbody = $mailbody . "<p>*This is auto generated email. Don't reply to this email.*</p>";
    $mailbody = $mailbody . "</body></html>";

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: Digital Menubox Lead <" . $mailArr[0][0] . ">" . "\r\n";
    $mailsent = mail($mailArr[0][0], $subject, $mailbody, $headers);

//    Mail setup ::: End
} else if ($action != '' && $action == "fetch_post_dropdown") {
    $question_query = "SELECT * from questions where qa_id='" . $qa_id . "' limit 1";
    $question_row = select("$question_query");
    $questionType = trim($question_row[0]['questionType']);
    $fieldType = trim($question_row[0]['field']);
    $assoc['dropdown_html'] = get_type($channel, $questionType, $fieldType);
//    $assoc['questionType'] = $questionType;
//    $assoc['fieldType'] = $fieldType;
    echo json_encode($assoc);
}

function chk_existent($brand) {
    global $channels;
    $query = "SELECT DISTINCT(handle) from $channels[$brand].brands WHERE brandStatus = 0 ORDER BY RAND()";
    $result = select($query);
    if (count($result) > 0) {
        $newHandles = array();
        for ($i = 0; $i < count($result); $i++) {
            $handle = $result[$i][0];
            if ($brand == 'facebook') {
                $query = "SELECT count(*) as usercnt from $channels[$brand].posts WHERE content != '' AND handle='" . $handle . "'";
                $post = select($query);

                $que = "SELECT count(*) as userpostcnt from $channels[$brand].userposts WHERE content != '' AND handle='" . $handle . "'";
                $userpost = select($que);
                 $que2 = "SELECT count(*) as engcnt from $channels[$brand].engagements WHERE content != '' AND handle='" . $handle . "' AND engagementType='comment'";
                $brandeng = select($que2);
//                $fetchrandom_engagementID = "select count(*) as engpostcnt,distinct(postID),engagementID from $channels[$channel_name].engagements where handle= '$handle' AND content!='' AND engagementType='comment'";
//                $engpost = select($fetchrandom_engagementID);&& ($engpost[0][0] > 10)

                if (($post[0][0] > 10) && ($userpost[0][0] > 10) && ($brandeng[0][0] > 10)) {
                    $newHandles[] = $handle;
                }
            } elseif ($brand == 'instagram') {
//                $query = "SELECT count(*) as usercnt from $channels[$brand].posts WHERE content != '' AND comments>0 AND handle='" . $handle . "'";
//                $user = select($query);
//
//                if ($user[0][0] > 0) {
                $newHandles[] = $handle;
//                }
            } elseif ($brand == 'twitter') {
                $query = "SELECT count(*) from $channels[$brand].posts WHERE content != '' AND handle='" . $handle . "'";
                $post = select($query);

                $que = "SELECT count(*) from $channels[$brand].userposts WHERE content != '' AND handle='" . $handle . "'";
                $userpost = select($que);

                $que = "SELECT count(*) from $channels[$brand].engagements WHERE content != '' AND handle='" . $handle . "'";
                $engage = select($que);

                if (($post[0][0] > 5) && ($userpost[0][0] > 5) && ($engage[0][0] > 5)) {
                    $newHandles[] = $handle;
                }
            } elseif ($brand == 'youtube') {
                $query = "SELECT count(*) from $channels[$brand].posts WHERE title != '' AND handle='" . $handle . "'";
                $post = select($query);

                if ($post[0][0] > 5) {
                    $newHandles[] = $handle;
                }
            }
        }
    }
    return $newHandles;
}

function get_type($channel, $table, $searchField) {
    global $channels;
    $db = $channels["$channel"];
    $html = '<select onchange="feel_input_answer();" id="get_type_dropdown" style="width: 89%; margin-left: 6px; float: left;">';
    if ($searchField == 'via') {
        $html .= "<option value=''>Not Known</option>";
    }
    
    if ($searchField == 'sex') {
        $html .= "<option value='male'>male</option>";
        $html .= "<option value='female'> female </option>";
    } else {
        $q = "SELECT distinct($searchField) FROM $db.$table";
        $res = select($q);
        $types = array();
        for ($i = 0; $i < count($res); $i++) {
            $types[] = $res[$i][0];
        } if (is_array($types) && $types != array()) {
            for ($i = 0; $i < count($types); $i++) {
                if ($types[$i] != '')
                    $html .= "<option value='" . $types[$i] . "'> $types[$i] </option>";
            }
        }
    }



    $html.="</select>";
    return $html;
}

?>