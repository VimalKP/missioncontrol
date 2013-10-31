<?php
    include_once('db_connection.php');
    $query="SELECT a.`qa_id` , a.`z_testid_fk` , a.`expected` , a.`input` , a.`passed` , q.`question` , q.`sampleSize` , q.`parent_qa_id` , q.`parent_passrate` 
            FROM answer a
            INNER JOIN questions q ON ( a.qa_id = q.qa_id ) 
            WHERE a.`z_testid_fk` =  '17'";
    $result=select($query);
    $assoc=array();
    for($i=0;$i<count($result);$i++){
        $assoc[$i]['qa_id']=$result[$i]['qa_id'];
        $assoc[$i]['z_testid_fk']=$result[$i]['z_testid_fk'];
        $assoc[$i]['expected']=$result[$i]['expected'];
        $assoc[$i]['input']=$result[$i]['input'];
        $assoc[$i]['passed']=$result[$i]['passed'];
        $assoc[$i]['question']=$result[$i]['question'];
        $assoc[$i]['sampleSize']=$result[$i]['sampleSize'];
        $assoc[$i]['parent_qa_id']=$result[$i]['parent_qa_id'];
        $assoc[$i]['parent_passrate']=$result[$i]['parent_passrate'];
    }
    $finaljson=  json_encode($assoc);
    print_r($finaljson);
?>