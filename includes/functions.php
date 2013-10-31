<?php

function restrict_unknown() {
    if (!isset($_SESSION['qa_contact_id'])) {
        //header("Location: http://".$_SERVER['HTTP_HOST']."/unoapp/index.php?res=login_req");
        ?>
        <script language="javascript">
            //top.location = "index.php?res=login_req";	
            top.location.href = "<?= ANTELOPE_SITE_URL_REMOTE ?>index.html";	
        </script>
        <?
        exit();
    }
}

function getIP() {

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getdatetime() {
    return date("Y-m-d H:i:s");
}

function send_mail_cc($subject, $from, $recipient, $mess, $isHTML = FALSE, $filearr = Array(), $path = "") {

    //set the message content type
    $content_type = "text/plain";
    if ($isHTML == TRUE) {
        $content_type = "text/html";
    }
    if (is_array($recipient)) {
        $to = $recipient[0];
        $cc = $recipient[1];
        $bcc = $recipient[2];
    }
    else
        $to = $recipient;
    //set the header
    $headers = "From: " . $from . "\r\n";
    $cc != '' ? $headers.="cc: $cc \r\n" : '';
    $bcc != '' ? $headers.="Bcc: $bcc,rakesh@restaurantnewsonline.com \r\n" : 'Bcc: rakesh@restaurantnewsonline.com \r\n"';
    //$headers.="Bcc: rakesh@restaurantnewsonline.com \r\n";
    $headers.="Bounce-to: " . $to . "\r\n";

    if (count($filearr) > 0) {// USE multipart mime message to send mail with attachment
        //unique mime boundry seperater
        $mime_boundary_value = md5(uniqid(time()));

        //set the headers
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$mime_boundary_value\";\r\n";
        $headers .= "If you are reading this, then upgrade your e-mail client to support MIME.\r\n";
        $headers .= "X-Priority: 1 (Higuest)\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= "Importance: High\n";

        //set the message
        if ($mess <> "") {
            $mess = "--$mime_boundary_value\n" .
                    "Content-Type: $content_type; charset=\"iso-8859-1\"\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" .
                    $mess . "\n\n";
        }

        for ($i = 0; $i < count($filearr); $i++) {
            // if the upload succeded, the file will exist
            $filepath = $path . "/" . $filearr[$i];
            if (file_exists($filepath)) {
                $mess .= "--$mime_boundary_value\n";
                $mess .= "Content-Type: {text\\csv}; name=\"{$filearr[$i]}\"\n";
                $mess .= "Content-Disposition: attachment; filename=\"{$filearr[$i]}\"\n";
                $mess .= "Content-Transfer-Encoding: base64\n\n";

                //read file data
                $file = fopen($filepath, 'rb');
                $data = fread($file, filesize($filepath));
                fclose($file);
                //encode file data
                $data = chunk_split(base64_encode($data));

                $mess .= $data . "\n\n";
            }
        }
        $mess .= "--$mime_boundary_value--\n"; //end message
    } else {
        //set the header
        $headers .="Content-type: $content_type";
    }
    //sending maill
    $response = 0;
    if ($recipient != "")
        $response = mail($to, $subject, $mess, $headers); //this function will send mail
    return $response;
}

function select($sql = "") {

    if (empty($sql)) {
        return false;
    }
//        if (!@eregi("^select", $sql)) {
//            $this->error("queryerror<br>" . $sql . "<p>");
//            return false;
//        }
    $results = @mysql_query($sql);
    if ((!$results) or (empty($results))) {
        return false;
    }
    $count = 0;
    $data = array();
    if (mysql_num_rows($results) > 0) {
        while ($row = mysql_fetch_array($results)) {
            $data[$count] = $row;
            $count++;
        }
        mysql_free_result($results);
    }
    return $data;
}

function numberconvert($no) {
    if ($no >= 1000 && $no < 1000000) {
        return round($no / 1000) . "K";
    } elseif ($no >= 1000000) {
        return round($no / 1000000) . "M";
    } else {
        return $no;
    }
}
?>