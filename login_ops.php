<?

ob_start();
session_start();
header("Cache-control: private, no-cache");
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
header("Cache: no-cahce");
ini_set("MAX_EXECUTION_TIME", "1800"); //30 minutes
include_once('db_connection.php');
include_once 'includes/functions.php';
//restrict_unknown();
$action = array_key_exists("action", $_POST) ? $_POST["action"] : "";
//echo $action;
if ($action == "checklogin") {
    //print_r($_POST);
    if ($_POST['existcookie'] == 'yes') {
        $username = base64_decode($_POST["username"]);
        $password = base64_decode($_POST["password"]);
    } else {
        $username = array_key_exists("username", $_POST) ? $_POST["username"] : "";
        $password = array_key_exists("password", $_POST) ? $_POST["password"] : "";
    }
    $logres = mysql_query("select * from contact where username='$username' and password='$password' and type='admin'");

    if ($_POST['existcookie'] == 'yes' && mysql_num_rows($logres) < 1) {
        setcookie("username", "", time() - 31536000);
        setcookie("password", "", time() - 31536000);
//        header("Location: ../index.php");
        session_destroy();
        echo "Your Password has been change please Login again...";
//        exit();
        die();
    }
    //	echo "select * from contact_mst where username='$username' and password='$password' ";
    if (mysql_num_rows($logres) < 1) {
        echo "Invalid Email or Password";
        die();
    }

    while ($logarr = mysql_fetch_array($logres)) {
//        if ($logarr['contact_wtaccess'] == 0) {
//            //print_r($logarr);
//            echo "Please verify your account to proceed, if you didn't receive an verification email, <a style='text-decoration:underline;'</a>";
//            die();
//        }
        if ($logarr['contact_status'] == 0 ) {
            echo "You account no longer exists, Email <a href='mailto:support@unoapp.com'>support@unoapp.com</a> if you feel this is an error.";
            die();
        }
        $_SESSION["qa_contact_id"] = $logarr['z_contactid_pk'];
//        $_SESSION["company_id"] = $logarr['z_companyid_fk'];
        //////// created to track the previousely selected company id
//        $_SESSION['companyid'] = (isset($_POST['comid']) && $_POST['comid'] != '') ? $_POST['comid'] : $logarr['z_companyid_fk'];
        ////////
        $_SESSION["qa_contact_name"] = $logarr['username'];
//        $_SESSION["contact_type"] = $logarr['type'];
//        $_SESSION["appAccessJson"] = $logarr['appAccessJson'];
        $_SESSION["is_login"] = mktime();
        if (isset($_POST['rememberme']) && $_POST['rememberme'] == 'checked') {
            setcookie("username", base64_encode($username), time() + 31536000);
            setcookie("password", base64_encode($password), time() + 31536000);
        }
        echo "#login_pass";

//        mysql_query("insert into loginlog_mst(z_contactid_fk,login_datetime,login_ip) values (" . $_SESSION["contact_id"] . ",'" . date("Y-m-d H:i:s") . "','" . getIP() . "')");
//        $_SESSION["loginlog_id"] = mysql_insert_id();
    }
    mysql_free_result($logres);
    mysql_query("insert into loginlog_mst(z_contactid_fk,login_datetime,login_ip) values (" . $_SESSION["qa_contact_id"] . ",'" . getdatetime() . "','" . getIP() . "')");
    $_SESSION["loginlog_id"] = mysql_insert_id();
}
//else if ($action == "sendpassword") {
//
//    $email = array_key_exists("email", $_POST) ? $_POST["email"] : "";
//    $conres = mysql_query("select * from contact_mst where contact_prmemail='$email'");
//
//    if (mysql_num_rows($conres) < 1) {
//        echo "Invalid Email";
//        die();
//    }
//    while ($conarr = mysql_fetch_array($conres)) {
//        //$email_url="http://".give_script_server()."/index.php";
//        $email_url = "http://www.digitalmarketingbox.com/unoapp";
//        $subject = "Login Details : unoapp.com";
//        $mailbody = "";
//        //$mailbody=$mailbody."<html><title>".$subject."</title><body>";
//        include "emailver.php";
//
//        $mailbody = $mailbody . "<p>Hello " . ucfirst(strtolower($conarr['contact_firstname'])) . " " . ucfirst(strtolower($conarr['contact_lastname'])) . "</p>
//			<p>Your login details for access UNOapp on unoapp.com is as below</p>
//			<p>Username : " . $conarr['username'] . "</p>" .
//                "<p>Password : " . $conarr['password'] . "</p>" .
//                "<p><a href='" . $email_url . "' target='_blank'>Click Here</a> to login in your account</p>";
//        $mailbody = $mailbody . "<p>If you have any questions, please email us at <a href='mailto:support@unoapp.com'>support@unoapp.com</a>.</p>";
//        $mailbody = $mailbody . "<p>&nbsp;</p><p>Thanks,</p>" .
//                "<p><b>UNOapp</b></p>";
//        $mailbody = $mailbody . "<br><p>* This is auto generated email. Don't reply to this email. *</p>";
//        $mailbody = email_custom($mailbody, ' <font color="#F73E5C"><b>UNO<i>app </i></b></font><span style="font-weight:lighter;">Login Credentials</span>', 'unoapp.png');
//        $mailbody = $mailbody . "</body></html>";
//
//        //echo $mailbody;
//
//        $headers = 'MIME-Version: 1.0' . "\r\n";
//        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//        $headers .= "From: UNOapp <welcome@unoapp.com>" . "\r\n";
//        //$headers .= "Bcc: ".AUTO_SENDER."\r\n";
//        $mailsent = mail($email, $subject, $mailbody, $headers);
//        echo "<span style='color:green;'>Account details sent to your email.</span>";
//    }
////} 
////else if ($action == "changepassword") {
////    $oldpassword = array_key_exists("oldpassword", $_POST) ? $_POST["oldpassword"] : "";
////    $newpassword = array_key_exists("newpassword", $_POST) ? $_POST["newpassword"] : "";
////    $conres = mysql_query("select * from contact_mst where z_contactid_pk=" . $_SESSION["contact_id"]);
////    if (mysql_num_rows($conres) > 0) {
////        $conarr = mysql_fetch_array($conres);
////        if ($oldpassword != $conarr["password"]) {
////            echo "Invalid : Old Password";
////        } else {
////            //Change Password in Filemaker
////            /* $fm = & new FileMaker();
////              $fm->setProperty('database', FM_DATABASE);
////              $fm->setProperty('hostspec', FM_HOST);
////              $fm->setProperty('username', FM_USERNAME);
////              $fm->setProperty('password', FM_PASSWORD);
////              ExitOnError($fm);
////
////              $confcmd=$fm->newFindCommand("TABLE Contact");
////              $confcmd->addFindCriterion('zk_ContactID_pk', $_SESSION["contact_id"]);
////              $confres = $confcmd->execute();
////              if (!(FileMaker::isError($confres))) {
////              $confrecs = $confres->getRecords();
////              foreach($confrecs as $confrec)
////              {
////              $fid=$confrec->getRecordId();
////              $editCommand=& $fm->newEditCommand("TABLE Contact",$fid);
////              $editCommand->setField('password',$newpassword);
////              $editCommand->execute();
////              }
////              } */
////            mysql_query("update contact_mst set password='$newpassword' where z_contactid_pk=" . $_SESSION["contact_id"]);
////            echo "Password Successfully Changed";
////        }
////    } else {
////        echo "Sorry : You can't modify your password. Please try again.";
////    }
////    mysql_free_result($conres);
////    activity_log_entry(" changed password");
//} 
else if ($action == "logout") {
    mysql_query("update loginlog_mst set logout_datetime='" . date("Y-m-d H:i:s") . "' where z_loginlogid_pk=" . $_SESSION["loginlog_id"]);
    setcookie("username", "", time() - 31536000);
    setcookie("password", "", time() - 31536000);
    unset($_SESSION["qa_contact_id"]);
    unset($_SESSION["qa_contact_name"]);
    unset($_SESSION["is_login"]);
    session_destroy();
    header("Location: index.html");
}
//else if ($action == "signup") {
////    foreach ($_POST as $form => $value) {
////        ${$form} = @clean($value);
////    }
////    $cntres = mysql_query("select * from contact_mst where username='$email'");
////    if (mysql_num_rows($cntres) > 0) {
////        $cntarr = mysql_fetch_array($cntres);
////        if ($cntarr["contact_wtaccess"] == 0) {
////            echo "Error: Your account has not been verified, if you didn't receive a verification email, please <a style='text-decoration:underline;' onclick='resend_verification(\"" . $cntarr['contact_prmemail'] . "\")'>click here</a> .";
////        } else {
////            echo "Error: Someone is already registered with same email.";
////        }
////    } else {
////        $cmpsql = "insert into company_mst(company_name) values('$company')";
////        mysql_query($cmpsql) or die("Unable to create account");
////        $companyid = mysql_insert_id();
////        $email_verification_code = generate_number_8();
////        $cntsql = "insert into contact_mst(z_companyid_fk,contact_firstname,contact_lastname,contact_wtaccess,username,password,contact_phone,contact_prmemail,newsletter,email_verification_code) values($companyid,'$firstname','$lastname',0,'$email','$password','$phone','$email','$register','$email_verification_code')";
////        mysql_query($cntsql) or die("Unable to create account");
////        $contactid = mysql_insert_id();
////        //echo $contactid;
////        echo "You will receive a verification email shortly.";
////        include "emailver.php";
////        $email_body = email_verfication_body($email_verification_code, $email);
////        $from = AUTO_SENDER;
////        $subject = "Thank you for registering at " . COMPANY;
////        send_mail($subject, $from, $email, $email_body, true);
////
////        $admin_body = "<p><b>ADMIN NOTIFICATION:</b></p><p>The following user verified their account.</p>
////				<p>Name: {$firstname} {$lastname}</p>
////				<p>Email: {$email}</p>
////				<p>Phone: {$phone}</p>
////				<p>Company Name: {$company}</p>
////				<p>Newsletter subscribed: " . ($register == 0 ? 'No' : 'Yes') . "</p>
////				<p>Date Time Registered: " . date("Y-m-d H:i:s") . "</p>
////				<p>IP Registered from: " . getIP() . "</p>
////				<p>Verification code: " . md5($email_verification_code) . "</p>";
////        $admin_body = email_custom($admin_body, '<font color="#F73E5C"><b>UNO<i>app</i> </b></font><span style="font-weight:lighter;">account</span> registered', 'unoapp.png');
////        $to = UNOAPP_ADMIN;
////        send_mail('ADMIN NOTIFICATION: UNOapp account registration notification.', $from, $to, $admin_body, true);
////    }
////} else if (!isset($_POST['action']) && $_GET['action'] == "signupverification") {
////    //$find_verificaion_code="select `email_verification_code` from contact_mst where `username`='".$_GET['email']."' 
////    $contact_query = mysql_query("select * from contact_mst where `username`='" . clean($_GET['email']) . "'") or die('No account is registered under this Email address.');
////    if (mysql_num_rows($contact_query) > 0) {
////        $contact_arr = mysql_fetch_assoc($contact_query);
////        $contactid = $contact_arr['z_contactid_pk'];
////        if ($contact_arr['contact_wtaccess'] == "1") {
////            echo 'Account has already been verified. Please login below.';
////            header("Location: ./index.php?signup_error=Account has already been verified. Please login below.");
////            exit();
////        }
////    } else {
////        echo 'No account is registered under this Email address.';
////        header("Location: ./index.php?signup_error=No account is registered under this Email address.");
////        exit();
////    }
////    $timestamp = date("Y-m-d H:i:s");
////    $check_verification_code = "update contact_mst set `contact_wtaccess`='1',`email_verification_datetime`='" . $timestamp . "' where `username`='" . $_GET['email'] . "' and md5(`email_verification_code`)='" . $_GET['code'] . "'  ;";
////    //echo $check_verification_code;
////    mysql_query($check_verification_code) or die("An error has occured, please contact support@UNOapp.com");
////    if (mysql_affected_rows() > 0) {
////        $reseller_query = mysql_query("select m.name,c.z_resellerid_fk from reseller_mst m left join company_mst c on c.z_resellerid_fk=m.z_resellerid_fk  where c.z_companyid_pk='" . $contact_arr['z_companyid_fk'] . "'") or die('No account is registered under this Email address.');
////        $reseller_arr = mysql_fetch_assoc($reseller_query);
////
////        if (!is_null($reseller_arr["name"])) {
////            $reseller_name = $reseller_arr["name"];
////        } else {
////            $reseller_name = "UNOapp";
////        }
////        if ($contact_arr['added_by'] == 100664) {
////            $reseller_name = "Gr8resos";
////        }
////        mysql_free_result($reseller_query);
////
////        $privilege = "insert into priviledge_mst set `z_contactid_fk`='" . $contact_arr['z_contactid_pk'] . "', `z_companyid_fk`='" . $contact_arr['z_companyid_fk'] . "';";
////        mysql_query($privilege) or die("An error has occured, please contact support@UNOapp.com");
////        echo "Thank you for registering with UNOapp, your account has been activated.";
////
////        include "emailver.php";
////        $from = AUTO_SENDER;
////        $admin_body = "<p><b>ADMIN NOTIFICATION:</b></p><p>The following user verified their account.</p>
////				<p>Name: {$contact_arr['contact_firstname']} {$contact_arr['contact_lastname']}</p>
////				<p>Email: {$contact_arr['contact_prmemail']}</p>
////				<p>Phone: {$contact_arr['contact_phone']}</p>
////				<p>Newsletter subscribed: " . ($contact_arr['newsletter'] == 0 ? 'No' : 'Yes') . "</p>
////				<p>Reseller: {$reseller_name}</p>  
////				<p>Date Time Verified: " . date("Y-m-d H:i:s") . "</p>
////				<p>IP Registered from: " . getIP() . "</p>
////				<p>Verification code: " . md5($_GET['code']) . "</p>";
////        $admin_body = email_custom($admin_body, '<font color="#F73E5C"><b>UNO<i>app </i></b></font><span style="font-weight:lighter;">account</span> verified', 'unoapp.png');
////        $to = UNOAPP_ADMIN;
////
////        send_mail('ADMIN NOTIFICATION: UNOapp account verified.', $from, $to, $admin_body, true);
////        lets_login($contactid);
////        header("Location: ./imagelist.php");
////        exit();
////    } else {
////        header("Location: ./index.php?signup_error=Error occured while verifying your account.");
////        exit();
////    }
////} else if ($action == 'resend_verification') {
////    $email = $_POST['email'];
////    $contact_res = mysql_query("select * from contact_mst where `contact_prmemail`='" . $email . "'");
////    if (mysql_num_rows($contact_res) < 1) {
////        echo "Error occured";
////        die();
////    }
////    $contact_arr = mysql_fetch_assoc($contact_res);
////    if ($contact_arr['contact_wtaccess'] == 0) {
////        include "emailver.php";
////        $email_body = email_verfication_body($contact_arr['email_verification_code'], $email);
////        $subject = "Thank you for registering at " . COMPANY;
////        send_mail($subject, AUTO_SENDER, $email, $email_body, true);
////        echo "<span style='color:green;'>You should receive your verfication email shortly</span>";
////    }
////} else if ($_REQUEST['action'] == 'review_report_login') {
////
////    $contactid = $_GET['contactid'];
////    $code = $_GET['code'];
////
////    $contact_arr = dquery("select * from contact_mst where `z_contactid_pk`='$contactid' and md5(`email_verification_code`)='$code'", true);
////    if ($contact_arr[0]['contact_wtaccess'] == 1) {
////        lets_login($contactid);
////        header("Location: ./imagelist.php?func=fetchreview2");
////    } else {
////        header("Location: ./index.php?signup_error=Error occured while verifying your account.");
////        exit();
////    }
////} else if ($_REQUEST['action'] == 'func_redirect') {
////    $contactcode = $_REQUEST['security'];
////    $func = $_REQUEST['func'];
////    $param = $_REQUEST['param'];
////    $contact_arr = dquery("select contact_wtaccess,z_contactid_pk from contact_mst where  sha1(`z_contactid_pk`)='$contactcode'", true);
////    //echo "select * from contact_mst where  sha1(`z_contactid_pk`)='$contactcode'";
////    //exit();
////    if ($contact_arr[0]['contact_wtaccess'] == 1) {
////        lets_login($contact_arr[0]['z_contactid_pk']);
////        header("Location: ./redirect.php?func=" . $func . "&param=" . $param . "");
////    } else if ($contact_arr[0]['contact_wtaccess'] == 0) {
////        lets_login($contact_arr[0]['z_contactid_pk']);
////        //header("Location: ./imagelist.php?func=".$func."&param=".$param."");				
////    } else {
////        header("Location: ./index.php?signup_error=Error occured while verifying your account.");
////        exit();
////    }
////} elseif ($_GET['action'] == 'confirmnotification') {
////    $contact_query = mysql_query("select * from contact_mst where `username`='" . clean($_GET['email']) . "'") or die('No account is registered under this Email address.');
////    if (mysql_num_rows($contact_query) > 0) {
////        $contact_arr = mysql_fetch_assoc($contact_query);
////        $contactid = $contact_arr['z_contactid_pk'];
////        mysql_query("UPDATE user_notifications SET confirm_notify='0' WHERE usernotfid=" . $_GET['notifyid']);
////        lets_login($contactid);
////        header("Location: ./imagelist.php");
////    } else {
////        header("Location: ./index.php");
////        exit();
////    }
////} else if ($_GET['action'] == "lcp") {
////
////    $arr = dquery("SELECT * FROM contact_mst where md5(`z_contactid_pk`)='" . $_GET['contact'] . "' and `contact_wtaccess`='1'");
////    if (count($arr) > 0) {
////        lets_login($arr[0]['z_contactid_pk']);
////        header("Location: ./redirect.php?func=fetch_lcp&param=dashboard&base64=" . base64_encode(json_encode(array("param" => "dashboard"))));
////    }
////} else if ($_REQUEST['action'] == 'func_redirect_webtool') {
////    $contactcode = $_REQUEST['security'];
////    $func = $_REQUEST['func'];
////    $param = $_REQUEST['param'];
////
////    $contact_arr = dquery("select contact_wtaccess,z_contactid_pk from contact_mst where  sha1(`z_contactid_pk`)='$contactcode'", true);
////    //echo "select * from contact_mst where  sha1(`z_contactid_pk`)='$contactcode'";
////    //exit();
////    //echo "select contact_wtaccess,z_contactid_pk from contact_mst where  sha1(`z_contactid_pk`)='$contactcode'";
////
////    if (count($contact_arr)) {
////        if ($contact_arr[0]['contact_wtaccess'] == 1) {
////            lets_login($contact_arr[0]['z_contactid_pk']);
////            header("Location: ./redirect.php?func=" . $func . "&param=" . $param . "");
////        } else if ($contact_arr[0]['contact_wtaccess'] == 0) {
////            header("Location: ./index.php?signup_error=Error occured while verifying your account.");
////            lets_login($contact_arr[0]['z_contactid_pk']);
////            //header("Location: ./imagelist.php?func=".$func."&param=".$param."");				
////        } else {
////            header("Location: ./index.php?r=signup&param=" . $param . "");
////            exit();
////        }
////    } else {
////        //$sql = "SELECT * FROM company_mst WHERE z_companyid_pk = ''";	
////        header("Location: ./index.php?r=signup&param=" . $param . "");
////        exit;
////    }
////} else if ($_REQUEST['action'] == 'signup_for_webtool') {
////    foreach ($_POST as $form => $value) {
////        ${$form} = @clean($value);
////    }
////    $cntres = mysql_query("select * from contact_mst where username='$email'");
////    if (mysql_num_rows($cntres) > 0) {
////        $cntarr = mysql_fetch_array($cntres);
////        if ($cntarr["contact_wtaccess"] == 0) {
////            echo "Error: Your account has not been verified, if you didn't receive a verification email, please <a style='text-decoration:underline;' onclick='resend_verification(\"" . $cntarr['contact_prmemail'] . "\")'>click here</a> .";
////        } else {
////            echo "Error: Someone is already registered with same email.";
////        }
////    } else {
////        if (!$companyid) {
////            //$cmpsql="insert into company_mst(company_name) values('$company')";
////            //mysql_query($cmpsql) or die("Unable to create account");
////            //$companyid=mysql_insert_id();
////        }
////
////        $email_verification_code = generate_number_8();
////
////        if ($contactid) {
////            $cntsql = "insert into 
////						contact_mst (z_contactid_pk, z_companyid_fk,contact_firstname,contact_lastname,contact_wtaccess,username,password,contact_phone,contact_prmemail,newsletter,email_verification_code) 
////							values($contactid,$companyid,'$firstname','$lastname',0,'$email','$password','$phone','$email','$register','$email_verification_code')";
////        } else {
////            $cntsql = "insert into contact_mst(z_companyid_fk,contact_firstname,contact_lastname,contact_wtaccess,username,password,contact_phone,contact_prmemail,newsletter,email_verification_code) values($companyid,'$firstname','$lastname',0,'$email','$password','$phone','$email','$register','$email_verification_code')";
////        }
////
////
////        mysql_query($cntsql) or die("Unable to create account");
////        $contactid = mysql_insert_id();
////        //echo $contactid;
////        echo "You will receive a verification email shortly.";
////        include "emailver.php";
////        $email_body = email_verfication_body($email_verification_code, $email);
////        $from = AUTO_SENDER;
////        $subject = "Thank you for registering at " . COMPANY;
////        send_mail($subject, $from, $email, $email_body, true);
////
////        $admin_body = "<p><b>ADMIN NOTIFICATION:</b></p><p>The following user verified their account.</p>
////				<p>Name: {$firstname} {$lastname}</p>
////				<p>Email: {$email}</p>
////				<p>Phone: {$phone}</p>
////				<p>Company Name: {$company}</p>
////				<p>Newsletter subscribed: " . ($register == 0 ? 'No' : 'Yes') . "</p>
////				<p>Date Time Registered: " . date("Y-m-d H:i:s") . "</p>
////				<p>IP Registered from: " . getIP() . "</p>
////				<p>Verification code: " . md5($email_verification_code) . "</p>";
////        $admin_body = email_custom($admin_body, '<font color="#F73E5C"><b>UNO<i>app</i> </b></font><span style="font-weight:lighter;">account</span> registered', 'unoapp.png');
////        $to = UNOAPP_ADMIN;
////        send_mail('ADMIN NOTIFICATION: UNOapp account registration notification.', $from, $to, $admin_body, true);
////    }
////} else if ($action == 'encrypt' && isset($_POST['c_name_value']) && $_POST['c_name_value'] != '' && isset($_POST['c_pass_value']) && $_POST['c_pass_value'] != '') {
////    echo base64_encode($_POST['c_name_value']) . "_" . base64_encode($_POST['c_pass_value']);
//} 
else {
    header("Location: ./index.php");
}
?>