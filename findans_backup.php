<?php

ob_start();
include_once 'db_connection.php';
session_start();
extract($_REQUEST);
$contactId = $_SESSION['qa_contact_id'];
$insert_update = '';
$find = trim($find);
for ($i = 0; $i < strlen($find); $i++) {
    if (is_numeric($find[0])) {
        $ar = array(",", "-");
        $find = str_replace($ar, "", $find);
    } else {
        break;
    }
}
$find_existing_qa = "SELECT * from answer  where z_testid_fk='$z_testid_pk' AND qa_id='" . $qa_id . "' AND handle='$handler'  limit 1";
$outresult = mysql_query($find_existing_qa);
$numcount = mysql_num_rows($outresult);
if ($numcount >= 1) {
    $insert_update = 'update';
} else {
    $insert_update = 'insert';
}
//$question_row = select("", "$question_query");
//$channels = array(
//    'facebook' => 'dmbdemo_facebook_agent',
//    'instagram' => 'dmbdemo_instagram',
//    'twitter' => 'dmbdemo_twitter_agent_standard',
//    'youtube' => 'dmbdemo_youtube_scrapper'
//);
$maindb = 'dmbdemo_agentsqa';
$DB_DATABASE = '';
$next_query = '';
//echo '<pre>';
//print_r($question_row);
//echo '<pre>';
//exit();
//$question_row = mysql_fetch_assoc(mysql_query($question_query));
//echo '<pre>';
//print_r($channels['facebook']);
//echo '<pre>';
//exit ();
if ($action != '' && $action == 'findans') {
//    if ($channel == 'facebook') {
//        echo 'in if';
//        $DB_DATABASE = $channels['facebook'];
//        selectdb("$DB_DATABASE");
//    } else if ($channel == 'instagram') {
//        $DB_DATABASE = $channels['instagram'];
//        selectdb("$DB_DATABASE");
//    } else if ($channel == 'twitter') {
//        $DB_DATABASE = $channels['twitter'];
//        selectdb("$DB_DATABASE");
//    } else {
//        $DB_DATABASE = $channels['youtube'];
//        selectdb("$DB_DATABASE");
//    }
    $assoc = array();
    $assoc['extra_id'] = '';
    $assoc['main_index'] = $main_index;
//    if ($find != '') {
    if ($z_testid_pk == '') {
        $insert_query = "INSERT INTO logtable (checked_date,z_contactid_fk,channel)values('" . getdatetime() . "','$contactId','$channel')";

        mysql_query($insert_query);
//            mysql_insert_id()
        $z_testid_pk = mysql_insert_id();
        $assoc['z_testid_pk'] = $z_testid_pk;
//            echo '$inserted_id--'.$inserted_id.'---'.$insert_query;
    } else {
        $assoc['z_testid_pk'] = $z_testid_pk;
    }
    $question_query = "SELECT * from questions where qa_id='" . $qa_id . "' limit 1";
    $question_row = select("$question_query");
    $query = trim($question_row[0]['query']);
    $samplesize = trim($question_row[0]['sampleSize']);
    $parent_qa_id = trim($question_row[0]['parent_qa_id']);
    $questionType = trim($question_row[0]['questionType']);
    $pattern1 = '/' . preg_quote('where1', '/') . '/'; //handle
    $pattern2 = '/' . preg_quote('where2', '/') . '/';
    $pattern3 = '/' . preg_quote('where3', '/') . '/'; //postid
    if (preg_match($pattern1, $query) && preg_match($pattern3, $query)) {
//        $assoc['pregmatchin']='yes';
        $assoc['pregmatch'] = '1';
        $next_query = str_replace('where1', $handler, trim($query));
        if (preg_match($pattern3, $next_query)) {
            $next_query = str_replace('where3', $extra_id, trim($next_query));
        }
    } else if (preg_match($pattern1, $query) && !preg_match($pattern3, $query)) {
        $assoc['pregmatch'] = '2';
//        $assoc['pregmatchin']='yes';
        $next_query = str_replace('where1', $handler, trim($query));
    } else if (!preg_match($pattern1, $query) && preg_match($pattern3, $query)) {
        $assoc['pregmatch'] = '3';
//        $assoc['pregmatchin']='yes';
        $next_query = str_replace('where3', $extra_id, trim($query));
    } else {
        $next_query = $query;
    }

    if (preg_match($pattern2, $next_query)) {
        $next_query = str_replace('where2', $find, trim($next_query));
    }

    if ($channel == 'facebook') {
        $DB_DATABASE = $channels['facebook'];
        selectdb("$DB_DATABASE");
    } else if ($channel == 'instagram') {
        $DB_DATABASE = $channels['instagram'];
        selectdb("$DB_DATABASE");
    } else if ($channel == 'twitter') {
        $DB_DATABASE = $channels['twitter'];
        selectdb("$DB_DATABASE");
    } else if ($channel == 'youtube') {
        $DB_DATABASE = $channels['youtube'];
        selectdb("$DB_DATABASE");
    }
    $result = select("$next_query");
//    $assoc['result'] = $result;
    $assoc['next_query'] = $next_query;
    $assoc['handler'] = $handler;
    $assoc['samplesize'] = $samplesize;
    $assoc['parent_qa_id'] = $parent_qa_id;
//    if (count($result) == 0) {
////        echo 'fail';
//        global $maindb;
//        selectdb("$maindb");
//        $insert_query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('" . $assoc['z_testid_pk'] . "','$contactId','" . getdatetime() . "','$handler','','$find','','-1')";
//        mysql_query($insert_query);
////        exit();
//    }
    $assoc['resultcount'] = count($result);
    if ($extra_id != '') {
        $assoc['extra_id'] = $extra_id;
    }
    if (count($result) == 1) {


        $expectedoutput = trim($result[0][0]);
        $assoc['expectedoutput'] = $expectedoutput;
        if (($qa_id == 7 || $qa_id == 21 || $qa_id == 33 || $qa_id == 39 || $qa_id == 40 || $qa_id == 108 || $qa_id == 122 || $qa_id == 55) && $extra_id != '') {
//            if ($qa_id == 21 || $qa_id = 39 || $qa_id = 40) {
            $assoc['in'] = 'inif count 21 39 40 33 108 55';
            $date = date("Y-m-d H:i:s", strtotime($find));
            if (strtotime($expectedoutput) == strtotime($find)) {
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $date, $expectedoutput);
            } else {
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $date, $expectedoutput);
            }
//            }
        } else if ($qa_id == 19 || $qa_id == 100) {
            $assoc['in'] = 'in ele if count 19';
            $date = date("Y-m-d H:i:s", strtotime($find));
            if (strtotime($expectedoutput) == strtotime($find)) {
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $date, $expectedoutput);
            } else {
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $date, $expectedoutput);
            }
        } else if ($qa_id == 44 || $qa_id == 1 || $qa_id == 120 || $qa_id == 90) {// user table
            $assoc['in'] = '44 and 1 120';
//            $friendly = trim($result[0][1]);

            if ($expectedoutput != '') {
                $assoc['extra_id'] = $expectedoutput;
//                $extra_id = $expectedoutput;
//                    echo pass($ans_id);
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $expectedoutput, $find, $find);
//                selectdb("$maindb");
//               / $insert_query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('" . $assoc['z_testid_pk'] . "','$contactId','" . getdatetime() . "','$handler','$extra_id','$find','$expectedoutput','1')";
//                mysql_query($insert_query);
            } else {
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, '', $find, '');
            }
        } else if ($extra_id != '' && $qa_id == 30) {//facebook if comment is 1
            $assoc['extra_id'] = '';
            $outcomment = trim($result[0][1]);
            if ($expectedoutput != '') {
                $assoc['extra_id'] = $expectedoutput;
//                $extra_id = $expectedoutput;
//                    echo pass($ans_id);
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $expectedoutput, $find, $outcomment);
//                selectdb("$maindb");
//               / $insert_query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('" . $assoc['z_testid_pk'] . "','$contactId','" . getdatetime() . "','$handler','$extra_id','$find','$expectedoutput','1')";
//                mysql_query($insert_query);
            } else {
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, '', $find, '');
            }
        } else if ($extra_id != '' && ($qa_id == 10 || $qa_id == 81 || $qa_id == 83 || $qa_id == 71 || $qa_id == 73 || $qa_id == 61 || $qa_id == 63)) {// instageam for comaa separed answer
            $find_array = explode(',', $find);
            $output_tf = '';
            for ($i = 0; $i < count($find_array); $i++) {
                $output_tf.=find_name($find_array[$i], $expectedoutput);
            }
            if (strpos("no", $output_tf)) {
//                echo fail($ans_id);
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
            } else {
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
//                echo pass($ans_id);
            }
//            exit();
        } else if ($extra_id != '' && $qa_id == 11) { //hash count 
            $find_array = explode(',', $expectedoutput);
            $assoc['in'] = '11';
//                $output_tf = '';
            if (count($find_array) == $find) {
                pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, count($find_array));
            } else {
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, count($find_array));
            }
//            exit();
        } else {
            $assoc['in'] = 'inelse count 1';
//            if ($qa_id == 10) {
//                $find_array = explode(',', $find);
//                $output_tf = '';
//                for ($i = 0; $i < count($find_array); $i++) {
//                    $output_tf.=find_name($find_array[$i], $output);
//                }
//                if (strpos("no", $output_tf)) {
//                    echo fail($ans_id);
//                } else {
//                    echo pass($ans_id);
//                }
//                exit();
//            }
//            if ($qa_id == 11) {
//                $find_array = explode(',', $output);
////                $output_tf = '';
//                if (count($find_array) == $find) {
//                    echo pass($ans_id);
//                } else {
//                    echo fail($ans_id);
//                }
//                exit();
//            }
//            if ($qa_id == 12 || $qa_id == 13 || $qa_id == 15) {
//                echo fail($ans_id);
//                exit();
//            }
            if (!is_numeric($expectedoutput)) {
                $assoc['answertype'] = ' text';
                if (strtolower(trim($find)) == strtolower($expectedoutput)) {
//                    echo pass($ans_id);
                    pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
//                selectdb("$maindb");
//               / $insert_query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('" . $assoc['z_testid_pk'] . "','$contactId','" . getdatetime() . "','$handler','$extra_id','$find','$expectedoutput','1')";
//                mysql_query($insert_query);
                } else {
                    fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
                }
            } else if (is_numeric($expectedoutput)) {
                $assoc['answertype'] = 'number';
                $tolerans = $question_row[0]['tolerance'] * 10;
                $new1 = $expectedoutput + ceil($expectedoutput * $tolerans) / 100;
                $new2 = $expectedoutput - ceil($expectedoutput * $tolerans) / 100;
                if ($find <= $new1 && $find >= $new2) {
                    pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
                } else {
                    fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
                }
            } else {
                $assoc['answertype'] = 'null but count1';
                fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput);
            }
//        echo '<pre>';
//        exit();
//        $next_query = str_replace("where2", trim($find), str_replace("where1", $handler, trim($question_row[0]['query'])));
//        if ($extra_id != '') {
//            $next_query = str_replace("where3", $extra_id, trim($next_query));
//        }
////    echo $next_query
//        $next_query_row = mysql_fetch_assoc(mysql_query($next_query));
//        if ($next_query_row['count'] == '1') {
//            $upadte_query = "update agentsqa set passes='1',tolerance='1.0' where qa_id='" . $qa_id . "'";
//            mysql_query($upadte_query);
//            if ($next_query_row['save_id'] != '') {
//                echo $next_query_row['save_id'];
//            } else {
//                echo "pass";
//            }
//        } else {
//            $upadte_query = "update agentsqa set passes='-1',tolerance='0.5' where qa_id='" . $qa_id . "'";
//            mysql_query($upadte_query);
//            echo "fail";
        }
    } else if (count($result) > 1) {
        $assoc['in'] = 'in else if count >1';
        if ($find != '') {
            for ($i = 0; $i < count($result); $i++) {
                $pid = $result[$i][0];
                $content = trim(stripslashes($result[$i][1]));
                $pattern = '/' . preg_quote($find, '/') . '/';
                $assoc['extra_id'] = '';
                if (preg_match($pattern, $content)) {
                    $assoc['extra_id'] = $pid;
                    pass($assoc['z_testid_pk'], $contactId, $qa_id, $handler, $pid, $find, $content);
                    break;
                }
//                if(strpos($content, "$find")){
//                     echo $pid;
//                    break;
//                }
            }
        }
        if ($assoc['extra_id'] == '') {
            $assoc['extra_id'] = '';
            fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, '', $find, '');
        }
    } else {
        $assoc['resultcount'] = 'result count is 0';
        fail($assoc['z_testid_pk'], $contactId, $qa_id, $handler, '', $find, '');
//        $assoc['extra_id'] = '';
    }
//    if (($parent_qa_id == 0) && ($samplesize >= 1)) {
    get_result();
//    }
    $assoc['insert_update'] = $insert_update;

    echo json_encode($assoc);
//    } else {
//        global $maindb;
//        echo 'fail';
//        selectdb("$maindb");
//        $upadte_query = "update answers set passed='0' where ans_id='" . $ans_id . "'";
//        mysql_query($upadte_query);
//    }
}

function find_name($name, $list) {
    $exploded = explode(',', $list);
    if (in_array($name, $exploded))
        return "yes";
    else
        return "no";
}

function pass($z_testid_pk, $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput) {
    global $maindb, $assoc, $insert_update;
    selectdb("$maindb");
    $query = '';
    if ($insert_update == 'insert') {
        $query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('$z_testid_pk','$contactId','" . getdatetime() . "','$qa_id','$handler','$extra_id','" . mysql_real_escape_string($find) . "','" . mysql_real_escape_string($expectedoutput) . "','1')";
        mysql_query($query);
    } else {
        $query = "UPDATE  answer SET z_contactid_fk='$contactId',date='" . getdatetime() . "',postID='$extra_id',passed='1',input='" . mysql_real_escape_string($find) . "',expected='" . mysql_real_escape_string($expectedoutput) . "' WHERE z_testid_fk='$z_testid_pk' AND qa_id='" . $qa_id . "' AND handle='$handler'";
        mysql_query($query);
    }

    $assoc['query'] = $query;
//    $assoc['outputresult'] = 'pass';
}

function fail($z_testid_pk, $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput) {
    global $maindb, $assoc, $insert_update;
    selectdb("$maindb");
    $query = '';
    if ($insert_update == 'insert') {
        $query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('$z_testid_pk','$contactId','" . getdatetime() . "','$qa_id','$handler','$extra_id','" . mysql_real_escape_string($find) . "','" . mysql_real_escape_string($expectedoutput) . "','-1')";
        mysql_query($query);
    } else {
        $query = "UPDATE  answer SET z_contactid_fk='$contactId',date='" . getdatetime() . "',postID='$extra_id',passed='-1',input='" . mysql_real_escape_string($find) . "',expected='" . mysql_real_escape_string($expectedoutput) . "' WHERE z_testid_fk='$z_testid_pk' AND qa_id='" . $qa_id . "' AND handle='$handler'";
        mysql_query($query);
    }
    $assoc['query'] = $query;
//    $assoc['outputresult'] = 'fail';
}

function get_result() {
    global $pre_qa_id, $samplesize, $channel, $z_testid_pk, $parent_qa_id, $handler, $assoc, $qa_id;
    $assoc['pre_qa_id'] = $pre_qa_id;
    $question_query = "SELECT q.samplesize,t.sampleSize as st from questions as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType where q.qa_id='" . $pre_qa_id . "' limit 1";
    $question_query = select($question_query);
    $presamplesize = $question_query[0][0];
//    $presamplesize = $question_query[0]['st'];
    $limitmain = $question_query[0]['st'] - 1;
//    $assoc['$samplesize']=$samplesize;
//    $assoc['$parent_qa_id']=$parent_qa_id;
    //condition of Grouped questions
    if ($pre_qa_id != 0 && $presamplesize > 1) {

        $assoc['getresult'] = 'getresult if';
        $find_pre_qa = "SELECT * from answer  where z_testid_fk='$z_testid_pk' AND qa_id='" . $pre_qa_id . "' AND handle='$handler' AND postID!='' limit 1";
        $outresult = mysql_query($find_pre_qa);
        if (mysql_num_rows($outresult) > 0) {
            $find_lastqaid = "SELECT qa_id from questions  where parent_qa_id='" . $pre_qa_id . "' AND query !=  ''  ORDER BY qa_id ASC LIMIT $limitmain";
            $find_lastqaid = select($find_lastqaid);
            $indexlastid=count($find_lastqaid)-1;
            $lastid = $find_lastqaid[$indexlastid][0];
            if (count($find_lastqaid) > 0 && $lastid == $qa_id) {       //fetch previous qa id's pass rate
                $query = "SELECT q.sampleSize,t.sampleSize as st,t.passrate as tt FROM `questions` as q JOIN tablewise_samplesize as t ON t.questionType=q.questionType WHERE q.qa_id ='$pre_qa_id' AND q.query !=  '' AND q.parent_qa_id = 0 and q.sampleSize>1";
                $parent_passrate = select($query);
//                $parent_passrate = $parent_passrate[0][0];
                $sampleSize = $parent_passrate[0]['sampleSize'];
                $tt = $parent_passrate[0]['tt'];
                $st = $parent_passrate[0]['st'];
                if ($sampleSize > $st) {
                    $passrate = ceil($st * $tt);
                } else {
                    $passrate = ceil($sampleSize * $tt);
                }
                $q = "SELECT qa_id FROM `questions` WHERE `channel` LIKE '$channel' and `parent_qa_id`=" . $pre_qa_id . " and `query`!=''";
                $subqueArr = select($q);
                $allsubQueIds = array();
                foreach ($subqueArr as $subvalue) {
                    $allsubQueIds[] = $subvalue['qa_id'];
                }
                $allsubQueIds = implode(',', $allsubQueIds);
                $qu = "SELECT count(*) as cnt
                    FROM  `answer` 
                    WHERE  `z_testid_fk` =$z_testid_pk AND `handle` =  '" . $handler . "' and  qa_id IN($allsubQueIds) AND passed=1";
                $cnt_passed = select($qu);
//            if (count($cnt_passed) > 0) {
                if ($passrate <= $cnt_passed[0]['cnt']) {
                    $assoc['outputresult'] = 'pass';
                    update_logtable();
                } else {
                    $assoc['outputresult'] = 'fail';
                }
            }
//            }
//            $assoc['outputresult'] = 'pass';
        } else {
            $assoc['outputresult'] = 'fail';
        }
    } else if ($samplesize == 1 && $parent_qa_id == 0) {        ////condition of single questions
        $assoc['getresult'] = 'getresult else if';
        $find_pre_qa = "SELECT passed from answer where z_testid_fk='$z_testid_pk' AND qa_id='" . $pre_qa_id . "' AND handle='$handler' limit 1";

        $outresult = select($find_pre_qa);
        $passed = $outresult[0][0];
//          $assoc['getresult_pass'] = $outresult;
        if ($passed == 1) {
            $assoc['outputresult'] = 'pass';
            update_logtable();
        } else {
            $assoc['outputresult'] = 'fail';
        }
    } else {
        $assoc['getresult'] = 'getresult else else';
        $assoc['outputresult'] = 'fail';
    }
}

function update_logtable() {
    global $channel, $z_testid_pk, $assoc;

    $que = "SELECT l.score,l.status,s.passrate,s.testsize from logtable as l join(setting as s) on (l.channel=s.channel) where l.z_testid_pk='$z_testid_pk' AND l.channel='$channel' ";
    $logArr = select($que);
    $preScore = $logArr[0]['score'];
//    $prestatus = $logArr[0]['status'];
//    $qu = "SELECT * from setting where channel='$channel' limit 1";
//    $settingArr = select($qu);
    $passrate = ceil($logArr[0]['passrate'] * $logArr[0]['testsize']);
    $score = $preScore++;
    if (($score) >= $passrate) {
        $newScore = ($score);
        $newStatus = 'pass';
    } else {
        $newScore = $preScore;
        $newStatus = 'fail';
    }
//    $assoc['update_logtable'] = $newScore . '  ---   ' . $newStatus . '   ---    ' . $z_testid_pk;
    $q = "UPDATE `logtable` SET score=$newScore ,status='$newStatus' WHERE `z_testid_pk`='$z_testid_pk'";
    mysql_query($q);
}

//function notanswer($z_testid_pk, $contactId, $qa_id, $handler, $extra_id, $find, $expectedoutput) {
//    global $maindb, $assoc, $insert_update;
//    selectdb("$maindb");
//    if ($insert_update == 'insert') {
//        $insert_query = "INSERT INTO answer (z_testid_fk,z_contactid_fk,date,qa_id,handle,postID,input,expected,passed) values('$z_testid_pk','$contactId','" . getdatetime() . "','$qa_id','$handler','$extra_id','" . mysql_real_escape_string($find) . "','" . mysql_real_escape_string($expectedoutput) . "','0')";
//        mysql_query($insert_query);
//    } else {
//        $update_query = "UPDATE  answer SET z_contactid_fk='$contactId',date='" . getdatetime() . "',postID='$extra_id',passed='0',input='" . mysql_real_escape_string($find) . "',expected='" . mysql_real_escape_string($expectedoutput) . "' WHERE z_testid_fk='$z_testid_pk' AND qa_id='" . $qa_id . "' AND handle='$handler'";
//        mysql_query($update_query);
//    }
//    $assoc['query'] = $insert_query;
//    $assoc['outputresult'] = 'fail';
//}
function selectdb($DB_DATABASE) {
//    echo "---" . $DB_DATABASE . '<br/>';
    mysql_select_db("$DB_DATABASE") or die("Unable to00 $DB_DATABASE select database");
}

?>