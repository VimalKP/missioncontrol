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
//include_once 'includes/functions.php';
global $channels;
extract($_POST);

if ($action != '' && $action == "getPage") {
    $globalArr = array();
    $dbName = $channels[$managepage];
    if ($managepage == 'facebook') {
        $hisType = 'ACT';
    } elseif ($managepage == 'instagram') {
        $hisType = 'followers';
    } elseif ($managepage == 'twitter') {
        $hisType = 'followers';
    } elseif ($managepage == 'youtube') {
        $hisType = 'Subscribers';
    }

    if ($search != '') {
        $qr = "SELECT `handle` FROM $dbName.brands WHERE brandStatus=0 AND handle LIKE '%$search%' order by handle ASC";
    } else {
        $qr = "SELECT `handle` FROM $dbName.brands WHERE brandStatus=0 order by handle ASC";
    }
    $q = mysql_query($qr);
    $total_pages = mysql_num_rows($q);
    $stages = 3;
    $limit = 20;
    if ($page) {
        $start = ($page - 1) * $limit;
    } else {
        $start = 0;
    }
//    $noPage = $rows / $limit;

    if ($search != '') {
        $query1 = "SELECT `handle` FROM $dbName.brands WHERE brandStatus=0 AND handle LIKE '%$search%' order by handle ASC LIMIT $start,$limit";
    } else {
        $query1 = "SELECT `handle` FROM $dbName.brands WHERE brandStatus=0 order by handle ASC LIMIT $start,$limit";
    }
//    echo $query1;

    $q = mysql_query($query1);
    $allBrands = array();
    while ($row = mysql_fetch_assoc($q)) {
        $allBrands[] = $row['handle'];
    }
  
    if (is_array($allBrands) && $allBrands != array()) {
        $globalArr = array();
        foreach ($allBrands as $handle) {
            $minquery = "SELECT `lifetime` as start_fans,`date` as first_date  FROM $dbName.historical WHERE `handle`='" . $handle . "' and `histType`='$hisType' ORDER BY `historical`.`date` ASC limit 1";
            $min = mysql_query($minquery);
            while ($minResult = mysql_fetch_assoc($min)) {
                $globalArr[$handle]['start_fans'] = $minResult['start_fans'];
                $globalArr[$handle]['first_date'] = $minResult['first_date'];
            }
            $maxquery = "SELECT `lifetime` as end_fans,`date` as last_date  FROM $dbName.historical WHERE `handle`='" . $handle . "' and `histType`='$hisType' ORDER BY `historical`.`date` DESC limit 1";
            $max = mysql_query($maxquery);
            while ($maxResult = mysql_fetch_assoc($max)) {
                $globalArr[$handle]['end_fans'] = $maxResult['end_fans'];
                $globalArr[$handle]['last_date'] = $maxResult['last_date'];
            }

            $engages = "SELECT engagements,engagedUser FROM $dbName.brands WHERE `handle`='" . $handle . "'";
            $engagement = select($engages);
            $globalArr[$handle]['engagements'] = $engagement[0][0];
            $globalArr[$handle]['users'] = $engagement[0][1];

//            $queUser = "SELECT COUNT(DISTINCT userID) as users FROM $dbName.engagements WHERE handle='" . $handle . "'";
//            $users = select($queUser);
//            $globalArr[$handle]['users'] = $users[0][0];
//            if ($managepage != 'instagram') {
//                $query1 = "SELECT COUNT(DISTINCT userID)  FROM $dbName.engagements WHERE handle='" . $handle . "'";
//                $users = select($query1);
//                $globalArr[$handle]['users'] = $users[0][0];
//            }
//            
//            $pagi = '<li><a href="javascript:void(0)" id="prePage">&#171;</a></li>';
//            for ($i = 0; $i < $noPage; $i++) {
//                $pagi.='<li><a href="javascript:goto_page(' . $i . ')" id="page_' . $i . '">' . intval($i + 1) . '</a></li>';
//            }
//            $pagi.='<li><a href="javascript:void(0)" id="nextPage">&#187;</a></li>';
//            $globalArr[$handle]['pagination'] = $pagi;

            if ($page == 0) {
                $page = 1;
            }
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($total_pages / $limit);
            $LastPagem1 = $lastpage - 1;
            $paginate = '';
            if ($lastpage > 1) {
                // First
                if ($page > 1) {
                    $paginate.= "<li><a href='#' onclick='goto_page(1)'> << </a></li>";
                } else {
                    $paginate.= "<li class='disabled'><a> << </a></li>";
                }

                // Previous
                if ($page > 1) {
                    $paginate.= "<li><a href='#' onclick='goto_page($prev)'> < </a></li>";
                } else {
                    $paginate.= "<li class='disabled'><a><</a></li>";
                }

                // Pages	
                if ($lastpage < 7 + ($stages * 2)) { // Not enough pages to breaking it up
                    for ($counter = 1; $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $paginate.= "<li class='active'><a>$counter</a></li>";
                        } else {
                            $paginate.= "<li><a href='#' onclick='goto_page($counter)'>$counter</a></li>";
                        }
                    }
                } elseif ($lastpage > 5 + ($stages * 2)) { // Enough pages to hide a few?
                    // Beginning only hide later pages
                    if ($page < 1 + ($stages * 2)) {
                        for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
                            if ($counter == $page) {
                                $paginate.= "<li class='active'><a>$counter</a></li>";
                            } else {
                                $paginate.= "<li><a href='#' onclick='goto_page($counter)'>$counter</a></li>";
                            }
                        }
                        $paginate.= "<li><a>...</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page($LastPagem1)'>$LastPagem1</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page($lastpage)'>$lastpage</a></li>";
                    }
                    // Middle hide some front and some back
                    elseif ($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
                        $paginate.= "<li><a href='#' onclick='goto_page(1)'>1</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page(2)'>2</a></li>";
                        $paginate.= "<li><a>...</a></li>";
                        for ($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
                            if ($counter == $page) {
                                $paginate.= "<li class='active'><a>$counter</a></span>";
                            } else {
                                $paginate.= "<li><a href='#' onclick='goto_page($counter)'>$counter</a></li>";
                            }
                        }
                        $paginate.= "<li><a>...</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page($LastPagem1)'>$LastPagem1</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page($lastpage)'>$lastpage</a></li>";
                    }
                    // End only hide early pages
                    else {
                        $paginate.= "<li><a href='#' onclick='goto_page(1)'>1</a></li>";
                        $paginate.= "<li><a href='#' onclick='goto_page(2)'>2</a></li>";
                        $paginate.= "<li><a>...</a></li>";
                        for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
                            if ($counter == $page) {
                                $paginate.= "<li class='active'><a>$counter</a></li>";
                            } else {
                                $paginate.= "<li><a href='#' onclick='goto_page($counter)'>$counter</a></li>";
                            }
                        }
                    }
                }

                // Next
                if ($page < $counter - 1) {
                    $paginate.= "<li><a href='#' onclick='goto_page($next)'> > </a></li>";
                } else {
                    $paginate.= "<li class='disabled'><a> > </a></li>";
                }

                // Last
                if ($page < $counter - 1) {
                    $paginate.= "<li><a href='#' onclick='goto_page($lastpage)'> >> </a></li>";
                } else {
                    $paginate.= "<li class='disabled'><a> >> </a></li>";
                }
            }
        }
    }
    $globalArr[$handle]['pagination'] = $paginate;
    echo json_encode($globalArr);
} elseif ($action != '' && $action == "addBrand") {
    $db = $channels[$selectedPage];

    $handlename = explode(",", $handle);
    if (count($handlename) > 0) {
        foreach ($handlename as $value) {
            if ($value != '') {
                $query1 = "INSERT INTO $db.brands SET handle='$value'";
                $var = mysql_query($query1);
            }
        }
    }
    echo $var;
} elseif ($action != '' && $action == "deleteBrand") {
    $db = $channels[$selectedPage];
    $query1 = "UPDATE $db.brands SET brandStatus=1 WHERE handle='$handle'";
    $var = mysql_query($query1);
    echo $var;
}
?>