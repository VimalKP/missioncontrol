<?php

ob_start();
include_once 'db_connection.php';
$channel = $_POST['channel'];
//$lastIdSql = "SELECT `qa_id` FROM `agentsqa` WHERE `channel` LIKE '%$channel%'  order by `qa_id` DESC limit 1";
//$lastId = mysql_query($lastIdSql);
//$lastId = mysql_fetch_row($lastId);
//$lastId = $lastId[0];
//
//$firstIdSql = "SELECT *  FROM `agentsqa` WHERE `channel` LIKE '%$channel%'  order by `qa_id` ASC limit 1";
//$firstId = mysql_query($firstIdSql);
//$firstId = mysql_fetch_row($firstId);
//$firstId = $firstId[0];
$queId = $_POST['qa_id'];
$ans_id = $_POST['ans_id'];
$index = $_POST['index'];
$sql = "SELECT a.qa_id,a.ans_id,q.channel, q.question, a.handle, q.query
FROM answers a
INNER JOIN questions q ON a.qa_id = q.qa_id
WHERE a.passed !=1
ORDER BY q.channel,a.handle,a.ans_id ASC";
$result = mysql_query($sql);
//    print_r($result);
//    exit();
$arr = array();
$arr[] = array(0, "no more question available !", 0, "", 0);
while ($res = mysql_fetch_assoc($result)) {
    if ($res['question'] != null || $res['question'] != '')
        $arr[] = array($res['qa_id'], trim($res['question']), $res['channel'], $res['handle'], $res['ans_id']);
}

//$index = 0;
if ($_POST['type'] == 'next' && ($index < count($arr) - 1)) {
//    $sql = "SELECT a.qa_id,a.ans_id,q.channel, q.question, a.handle, q.query
//FROM answers a
//INNER JOIN questions q ON a.qa_id = q.qa_id
//WHERE a.passed !=1
//AND ans_id >$ans_id
//ORDER BY q.channel,a.handle,a.ans_id ASC 
//LIMIT 1 ";
//    $sql = "SELECT a.qa_id,a.ans_id,q.channel, q.question, a.handle, q.query
//FROM answers a
//INNER JOIN questions q ON a.qa_id = q.qa_id
//WHERE a.passed !=1
//AND ans_id >$ans_id
//ORDER BY a.ans_id ASC
//LIMIT 1 ";
//    $sql = "SELECT * FROM `questions` WHERE  passes!=1 AND qa_id> $queId order by `qa_id` ASC LIMIT 1";
//    $sql = "SELECT * FROM `agentsqa` WHERE `channel` LIKE '%$channel%' AND passes!=1 AND qa_id> $queId order by `qa_id` ASC LIMIT 1";
//    echo $sql;
//    $result = mysql_query($sql);
////    print_r($result);
////    exit();
//    $arr = array();
//    while ($res = mysql_fetch_assoc($result)) {
//        if ($res['question'] != null || $res['question'] != '')
//            $arr[] = array($res['qa_id'], trim($res['question']), $res['channel'], $res['handle'], $res['ans_id']);
//    }
//if ($_POST['type'] == 'next' && ($index < count($arr) - 1)) {
    $index = $index + 1;
//    $mode = next($arr);
} else if ($_POST['type'] == 'prev' && $index >= 1) {
    $index = $index - 1;
//    $index = $index - 1;
//    $mode = prev($arr);
//    $sql = "SELECT a.qa_id,a.ans_id,q.channel, q.query, a.handle, q.question
//FROM answers a
//INNER JOIN questions q ON a.qa_id = q.qa_id
//WHERE a.passed !=1
//AND ans_id <$ans_id
//ORDER BY q.channel,a.handle,a.ans_id DESC 
//LIMIT 1 ";
//    $sql = "SELECT a.qa_id,a.ans_id,q.channel, q.query, a.handle, q.question
//FROM answers a
//INNER JOIN questions q ON a.qa_id = q.qa_id
//WHERE a.passed !=1
//AND ans_id <$ans_id
//ORDER BY a.ans_id DESC
//LIMIT 1 ";
//    $sql = "SELECT * FROM `questions` WHERE  passes!=1 AND qa_id<$queId order by `qa_id` DESC LIMIT 1";
//    $sql = "SELECT * FROM `agentsqa` WHERE `channel` LIKE '%$channel%' AND passes!=1 AND qa_id<$queId order by `qa_id` DESC LIMIT 1";
//    $result = mysql_query($sql);
//    $arr = array();
//    while ($res = mysql_fetch_assoc($result)) {
//        if ($res['question'] != null || $res['question'] != '')
//            $arr[] = array($res['qa_id'], trim($res['question']), $res['channel'], $res['handle'], $res['ans_id']);
//    }
}
$resu = array(
    'question' => $arr[$index][1],
    'nextQueId' => $arr[$index][0],
    'index' => $index,
    'channel' => $arr[$index][2],
    'handle' => $arr[$index][3],
    'ans_id' => $arr[$index][4]
);

echo json_encode($resu);
//print_r($arr);
mysql_close();
ob_flush();
?>
