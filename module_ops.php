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
include("includes/FusionCharts_Gen.php");
//include_once 'includes/functions.php';
global $channels;
extract($_POST);

if ($action != '' && $action == "addmodule") {
    $query1 = "INSERT INTO module SET status='0',z_contactid_fk='" . $_SESSION['qa_contact_id'] . "', date_added='" . date('Y-m-d') . "'";
    $var = mysql_query($query1);
    echo mysql_insert_id();
//    echo 4;
} elseif ($action != '' && $action == "addtakeaway") {
    $prio = "SELECT priority FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority DESC LIMIT 1";
    $prio = select($prio);
    $nextPrio = $prio[0]['priority'] + 1;

    $moduleQuery = "SELECT channel from module WHERE module_id='" . $moduleId . "'";
    $moduleData = select($moduleQuery);
    $channel = $moduleData[0]['channel'];
    $db = $channels[$channel];

    mysql_select_db("$db") or die("Unable to $db select database");
    $whereHandle = '';
    if ($brandIds != "")
        $whereHandle = "'" . implode("','", $brandIds) . "'";
    $date = "'" . str_replace(' - ', '" and "', $date) . "'";

    $orgQuery = $query;
    if (strpos(strtolower($query), '{handle}') !== false) {
        if ($brandIds != '')
            $query = str_replace('{handle}', $whereHandle, $query);
    }
    if (strpos(strtolower($query), '{date}') !== false) {
        if ($date != "\"\"")
            $query = str_replace('{date}', $date, $query);
    }

    if (strpos(strtolower($query), 'limit') === false) {
        $query.=" LIMIT 1";
    }

    $output = select($query);

    $output_query = $question;
    if (strpos(strtolower($output_query), '{0}') !== false) {
        if ($output[0][0] != '')
            $output_query = str_replace('{0}', $output[0][0], $output_query);
    }
    if (strpos(strtolower($output_query), '{1}') !== false) {
        if ($output[0][1] != '')
            $output_query = str_replace('{1}', $output[0][1], $output_query);
    }

    mysql_select_db(DB_DATABASE) or die("Unable to select database");
    $query1 = "INSERT INTO takeaways SET module_id_fk='" . $moduleId . "', takeaway_question='" . $question . "', query='" . ($orgQuery) . "',output_query='" . $output_query . "', priority='" . $nextPrio . "', status='1'";

    $var = mysql_query($query1);

    $takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
    $takeawayData = select($takeQue);
    $html = '';
    if ($takeawayData != array()) {
        foreach ($takeawayData as $value) {
            $html.= '<li style="border: 1px solid black;padding-left: 7px;padding-top: 7px; background-color: #FFFFFF; cursor: pointer;margin-bottom: -1px;">
                    <input type="hidden" name="takeaways_sortorder[]" value="' . $value["takeaway_id"] . '" >
                    <span>' . $value['output_query'] . '</span>
                    <div class="takeaways-button">
                        <div class="delete-button"><a href="" data-toggle="modal" onclick="javascript:get_tkaw(\'' . $value["takeaway_id"] . '\');"><img src="images/edit_takeaway.png" alt="Edit" width="24" height="24"></a></div>
                        <div class="delete-button"><a href="#delModal" data-toggle="modal" onclick="javascript:$(\'#delId\').val(\'' . $value["takeaway_id"] . '\');"><img src="images/delete-icon.png" alt=""></a></div>
                        <div class="sorting-button"><a href="#"><img src="images/sorting-icon.png" alt=""></a></div>
                    </div>
                </li>';
        }
    }
    echo $html;
} elseif ($action != '' && $action == "deletetakeaway") {
    $query2 = "UPDATE `takeaways` SET status='0' WHERE takeaway_id='$tk_id'";
    $var = mysql_query($query2);

    $takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
    $takeawayData = select($takeQue);
    $html = '';
    if ($takeawayData != array()) {
        foreach ($takeawayData as $value) {
            $html.= '<li style="border: 1px solid black;padding-left: 7px;padding-top: 7px; background-color: #FFFFFF; cursor: pointer;margin-bottom: -1px;">
                    <input type="hidden" name="takeaways_sortorder[]" value="' . $value["takeaway_id"] . '" >
                    <span>' . $value['output_query'] . '</span>
                    <div class="takeaways-button">
                        <div class="delete-button"><a href="" data-toggle="modal" onclick="javascript:get_tkaw(\'' . $value["takeaway_id"] . '\');"><img src="images/edit_takeaway.png" alt="Edit" width="24" height="24"></a></div>
                        <div class="delete-button"><a href="#delModal" data-toggle="modal" onclick="javascript:$(\'#delId\').val(\'' . $value["takeaway_id"] . '\');"><img src="images/delete-icon.png" alt=""></a></div>
                        <div class="sorting-button"><a href="#"><img src="images/sorting-icon.png" alt=""></a></div>
                    </div>
                </li>';
        }
    }

    echo $html;
} elseif ($action != '' && $action == "updateSortOrder") {
    foreach ($tkIds as $key => $value) {
        $query = "update takeaways set priority='" . $key . "' where takeaway_id='" . $value . "'";
        mysql_query($query);
    }
    echo true;
} elseif ($action != '' && $action == "fillModule") {
    $global = array();
    $moduleQuery = "SELECT brands,chartType,axis_selector,competitor from module WHERE module_id='" . $moduleId . "'";
    $moduleData = select($moduleQuery);
    $sel_brands = $moduleData[0]['brands'];
    $sel_chartType = $moduleData[0]['chartType'];
    $axis_selector = $moduleData[0]['axis_selector'];
    $competitor = $moduleData[0]['competitor'];
//    if ($competitor != '') {
//        $competitor = explode(", ", $competitor);
//    }

    $axis_selector = json_decode($axis_selector, true);

    if (is_array($axis_selector['y1_axis_select'])) {
        $y1_axis_select = implode(",", $axis_selector['y1_axis_select']);
    }

    $global['x_axis_select'] = $axis_selector['x_axis_select'];
    $global['y1_axis_select'] = $y1_axis_select;
    $global['y2_axis_select'] = $axis_selector['y2_axis_select'];
    $global['x_axis_lbl'] = $axis_selector['x_axis_lbl'];
    $global['y1_axis_lbl'] = $axis_selector['y1_axis_lbl'];
    $global['y2_axis_lbl'] = $axis_selector['y2_axis_lbl'];

//    $global['competitor'] = $competitor;
//    $sql = "SELECT c.* FROM client_mst AS c join (clienttobrand as cb) on(c.z_clientid_pk=cb.z_clientid_fk) WHERE c.ownbrand='0' AND cb.channel='" . $channelName . "' AND cb.status='0'";
//    $competitors = select($sql);
//    $competitorsHtml = '';
//    for ($ind = 0; $ind < count($competitors); $ind++) {
//        $client_name = $competitors[$ind]['client_name'];
//        $clientid = $competitors[$ind]['z_clientid_pk'];
//        if (is_array($competitor) && in_array($client_name, $competitor)) {
//            $competitorsHtml.="<option value='" . $client_name . "' selected>" . $client_name . "</option>";
//        } else {
//            $competitorsHtml.="<option value='" . $client_name . "'>" . $client_name . "</option>";
//        }
//    }
//
//    $global['competitorsHtml'] = $competitorsHtml;

    $dbName = $channels[$channelName];
    $que = "SELECT * FROM $dbName.brands WHERE brandStatus=0";
    $brandData = select($que);
    $html = '';
    $competitorshtml = '';
    if (count($brandData) > 0)
        foreach ($brandData as $value) {

            if (strpos($sel_brands, $value['handle']) !== false) {
                $html.="<option value='" . $value['handle'] . "' selected='selected'>" . $value['handle'] . "</option>";
            } else {
                $html.="<option value='" . $value['handle'] . "'>" . $value['handle'] . "</option>";
            }
        }

    $global['brand'] = $html;

    foreach ($brandData as $value) {
        $disable = '';
        if (strpos($sel_brands, $value['handle']) !== false) {
            $disable = "disabled='disabled'";
        }

        if (strpos($competitor, $value['handle']) !== false) {
            $competitorshtml.="<option value='" . $value['handle'] . "' selected='selected' $disable>" . $value['handle'] . "</option>";
        } else {
            $competitorshtml.="<option value='" . $value['handle'] . "' $disable>" . $value['handle'] . "</option>";
        }
    }

    $global['competitor'] = $competitorshtml;

    $global['chartType'] = $sel_chartType;
    echo json_encode($global);
} elseif ($action != '' && $action == "updateModuleData") {

    $fields = array();
    $fields['x_axis_select'] = $x_axis_select;
    $fields['y1_axis_select'] = $y1_axis_select;
    if ($y2_axis_select != '') {
        $fields['y2_axis_select'] = $y2_axis_select;
        $fields['y2_axis_lbl'] = $y2_axis_lbl;
    }
    $fields['x_axis_lbl'] = $x_axis_lbl;
    $fields['y1_axis_lbl'] = $y1_axis_lbl;

    $fields = json_encode($fields);
    $query1 = "UPDATE module SET status='1',module_name='$name',internal_module_name='$internalName',channel='$channel',section='$objective',question='$question',data_query='" . addslashes($data_point) . "', query_output='" . addslashes($query_output) . "', brands='" . $selected_pages . "',daterange='" . $dateTB . "',chartType='" . $chartType . "',chartAttr='" . mysql_real_escape_string(addslashes($custom_chart_attr)) . "',axis_selector='" . mysql_real_escape_string($fields) . "',competitor='" . mysql_real_escape_string($selected_competitor) . "' WHERE module_id='$moduleId'";

    $var = mysql_query($query1);
    if ($var) {
        echo true;
    } else {
        echo mysql_error();
    }
} elseif ($action != '' && $action == "deletemodule") {
    $query1 = "UPDATE `module` SET status='0' WHERE module_id='$moduleId'";
    $var = mysql_query($query1);

    $query2 = "UPDATE `takeaways` SET status='0' WHERE module_id_fk='$moduleId'";
    $var = mysql_query($query2);
    echo true;
} elseif ($action != '' && $action == "drawChart") {
    $globalArr = array();

    $moduleQuery = "SELECT data_query,channel,chartAttr,axis_selector from module WHERE module_id='" . $moduleId . "'";
    $moduleData = select($moduleQuery);
    $query = stripcslashes($moduleData[0]['data_query']);
    $channel = $moduleData[0]['channel'];
    $chartAttr = $moduleData[0]['chartAttr'];

//    json_decode(stripslashes($chartAttr));

    $axis_selector = $moduleData[0]['axis_selector'];
    $axis_selector = json_decode($axis_selector, true);

    if (!is_array($y1_axis_select)) {
        $y1_axis_select = $axis_selector['y1_axis_select'];
    }
    if ($x_axis_select == '') {
        $x_axis_select = $axis_selector['x_axis_select'];
    }
    if ($y2_axis_select == '') {
        $y2_axis_select = $axis_selector['y2_axis_select'];
    }

    $globalArr['x_axis_select'] = $x_axis_select;
    $globalArr['y1_axis_select'] = $y1_axis_select;
    $globalArr['y2_axis_select'] = $y2_axis_select;
    $globalArr['x_axis_lbl'] = $x_axis_lbl;
    $globalArr['y1_axis_lbl'] = $y1_axis_lbl;
    $globalArr['y2_axis_lbl'] = $y2_axis_lbl;
    $axis_selector = $globalArr;

    $db = $channels[$channel];
    $next_query = $query;

    $whereHandle = '';
    if ($brandIds != "")
        $whereHandle = "'" . implode("','", $brandIds) . "'";
    $whereComp = '';
    if ($competitor != "")
        $whereComp = "'" . implode("','", $competitor) . "'";

    $allBrand = $whereHandle . "," . $whereComp;
    $allBrand = trim($allBrand, ",");

    $wheredate = '"' . str_replace(' - ', '" and "', $dates) . '"';
    $dateArr = explode(' - ', $dates);
    $sdate = $dateArr[0];
    $edate = $dateArr[1];

    if (strpos(strtolower($next_query), '{sdate}') !== false) {
        $next_query = str_replace('{sdate}', '"' . $sdate . '"', $next_query);
    }
    if (strpos(strtolower($next_query), '{edate}') !== false) {
        $next_query = str_replace('{edate}', '"' . $edate . '"', $next_query);
    }
    if (strpos(strtolower($next_query), '{handle}') !== false) {
        $next_query = str_replace('{handle}', $allBrand, $next_query);
    }

    if (strpos(strtolower($next_query), '{brand}') !== false) {
        $next_query = str_replace('{brand}', $whereHandle, $next_query);
    }
    if (strpos(strtolower($next_query), '{competitor}') !== false) {
        $next_query = str_replace('{competitor}', $whereComp, $next_query);
    }
    if (strpos(strtolower($next_query), '{date}') !== false) {
        if ($wheredate != "\"\"")
            $next_query = str_replace('{date}', $wheredate, $next_query);
    }

    mysql_select_db("$db") or die("Unable to $db select database");
    $next_query = str_replace("WHERE  AND", "where ", $next_query);

    $globalArr['result_query'] = $next_query;

    $outputArr = select($next_query);

    $fieldArr = array();
    $fieldHtml = '';
    if (count($outputArr[0]) > 0) {
        foreach ($outputArr[0] as $key => $value) {
            if (!is_numeric($key)) {
                $fieldArr[] = $key;
                $sel = "";
                if (is_array($axis_selector['y1_axis_select']) && in_array($key, $axis_selector['y1_axis_select'])) {
                    $sel = "selected";
                }
                $fieldHtml.="<option value='$key' $sel>$key</option>";
            }
        }
    }

    $globalArr['fields'] = $fieldHtml;

    $variable = getChartVariable($chartType, $outputArr, $chartAttr, $axis_selector);


    $globalArr['chart'] = stripcslashes(trim($variable));
    $globalArr['query_output'] = json_encode($outputArr);

    echo json_encode($globalArr);
} elseif ($action != '' && $action == "redrawChart") {
    $globalArr = array();

    $globalArr['x_axis_select'] = $x_axis_select;
    $globalArr['y1_axis_select'] = $y1_axis_select;
    $globalArr['y2_axis_select'] = $y2_axis_select;
    $globalArr['x_axis_lbl'] = $x_axis_lbl;
    $globalArr['y1_axis_lbl'] = $y1_axis_lbl;
    $globalArr['y2_axis_lbl'] = $y2_axis_lbl;
    $axis_selector = $globalArr;

    $qr = "SELECT query_output,chartAttr FROM module WHERE module_id='$moduleId'";

    $out = select($qr);
    $outputArr = json_decode($out[0]['query_output'], true);
    $chartAttr = $out[0]['chartAttr'];

    $fieldHtml = '';
    if (count($outputArr[0]) > 0) {
        foreach ($outputArr[0] as $key => $value) {
            if (!is_numeric($key)) {
                $fieldArr[] = $key;
                $sel = "";
                if (is_array($axis_selector['y1_axis_select']) && in_array($key, $axis_selector['y1_axis_select'])) {
                    $sel = "selected";
                }
                $fieldHtml.="<option value='$key' $sel>$key</option>";
            }
        }
    }

    $globalArr['fields'] = $fieldHtml;

    $variable = getChartVariable($chartType, $outputArr, $chartAttr, $axis_selector);

    $globalArr['chart'] = stripcslashes(trim($variable));

    echo json_encode($globalArr);
} elseif ($action != '' && $action == "getPage") {
    $globalArr = array();

//    $moduleQue = "SELECT * FROM module WHERE status='1'";
//    $modules = mysql_query($moduleQue);
    if ($search != '') {
        $qr = "SELECT * FROM module WHERE status='1' $search ORDER BY module_name ASC";
    } else {
        $qr = "SELECT * FROM module WHERE status='1' ORDER BY module_name ASC";
    }

    $modules = mysql_query($qr);
    $total_pages = mysql_num_rows($modules);
    $stages = 3;
    $limit = 20;
    if ($page) {
        $start = ($page - 1) * $limit;
    } else {
        $start = 0;
    }

    if ($search != '') {
        $query1 = "SELECT * FROM module WHERE status='1' $search ORDER BY module_name ASC LIMIT $start,$limit";
    } else {
        $query1 = "SELECT * FROM module WHERE status='1' ORDER BY module_name ASC LIMIT $start,$limit";
    }
//    echo $query1;

    $moduleData = select($query1);

//    $allBrands = array();
//    while ($row = mysql_fetch_assoc($q)) {
//        $allBrands[] = $row['handle'];
//    }
//    $html = '';
    if ($moduleData != array() && count($moduleData) > 0) {
        foreach ($moduleData as $value) {
            $moduleId = $value['module_id'];
            $takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
            $takeawayData = select($takeQue);
            $globalArr[$moduleId]['module_name'] = $value['module_name'];
            $globalArr[$moduleId]['channel'] = $value['channel'];
            $globalArr[$moduleId]['section'] = $value['section'];
            $globalArr[$moduleId]['question'] = $value['question'];
            $globalArr[$moduleId]['takeaway'] = count($takeawayData);
//            $html.='<tr><td>' . $value['module_name'] . '</td>';
//            $html.='<td>' . $value['channel'] . '</td>';
//            $html.='<td>' . $value['section'] . '</td>';
//            $html.='<td>' . $value['question'] . '</td>';
//            $html.='<td>' . count($takeawayData) . '</td>';
//            $html.='<td>
//                                    <a href="edit_modules.php?id=' . $moduleId . '"><img src="images/icon-edit.png"></a>
//                                    <a data-toggle="modal" href="#delModal" onclick="javascript:$(\'#delId\').val(\'' . $moduleId . '\');"><img src="images/icon-delete.png"></a>
//                                </td></tr>';
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
//    else {
//        $globalArr['content'][] = '<tr><td colspan="6"> Module Data not available! </td></tr>';
//    }
//    $globalArr['content'] = $html;
    $globalArr[$moduleId]['pagination'] = $paginate;
    echo json_encode($globalArr);
} elseif ($action != '' && $action == "getTakeaway") {
    $takeQue = "SELECT * FROM takeaways WHERE takeaway_id='" . $takeId . "' AND status='1'";
    $takeawayData = select($takeQue);
    echo json_encode($takeawayData[0]);
} elseif ($action != '' && $action == "edittakeaway") {

    $moduleQuery = "SELECT channel from module WHERE module_id='" . $moduleId . "'";
    $moduleData = select($moduleQuery);
    $channel = $moduleData[0]['channel'];
    $db = $channels[$channel];
    mysql_select_db("$db") or die("Unable to $db select database");
    $whereHandle = '';

    if ($brandIds != "")
        $whereHandle = "'" . implode("','", $brandIds) . "'";
    $date = "'" . str_replace(' - ', '" and "', $date) . "'";

    $orgQuery = $query;

    if (strpos(strtolower($query), '{handle}') !== false) {
        if ($brandIds != '')
            $query = str_replace('{handle}', $whereHandle, $query);
    }

    if (strpos(strtolower($query), '{date}') !== false) {
        if ($date != "\"\"")
            $query = str_replace('{date}', $date, $query);
    }

    if (strpos(strtolower($query), 'limit') === false) {
        $query.=" LIMIT 1";
    }

    $output = select($query);

    $output_query = $question;
    if (strpos(strtolower($output_query), '{0}') !== false) {
        if ($output[0][0] != '')
            $output_query = str_replace('{0}', $output[0][0], $output_query);
    }
    if (strpos(strtolower($output_query), '{1}') !== false) {
        if ($output[0][1] != '')
            $output_query = str_replace('{1}', $output[0][1], $output_query);
    }

    mysql_select_db(DB_DATABASE) or die("Unable to select database");
    $query = "update takeaways set takeaway_question='" . $question . "',query='" . $orgQuery . "',output_query='" . $output_query . "' where takeaway_id='" . $takeawayId . "'";
    mysql_query($query);

    $takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
    $takeawayData = select($takeQue);
    $html = '';
    if ($takeawayData != array()) {
        foreach ($takeawayData as $value) {
            $html.= '<li style="border: 1px solid black;padding-left: 7px;padding-top: 7px; background-color: #FFFFFF; cursor: pointer;margin-bottom: -1px;">
                    <input type="hidden" name="takeaways_sortorder[]" value="' . $value["takeaway_id"] . '" >
                    <span>' . $value['output_query'] . '</span>
                    <div class="takeaways-button">
                        <div class="delete-button"><a href="" data-toggle="modal" onclick="javascript:get_tkaw(\'' . $value["takeaway_id"] . '\');"><img src="images/edit_takeaway.png" alt="Edit" width="24" height="24"></a></div>
                        <div class="delete-button"><a href="#delModal" data-toggle="modal" onclick="javascript:$(\'#delId\').val(\'' . $value["takeaway_id"] . '\');"><img src="images/delete-icon.png" alt=""></a></div>
                        <div class="sorting-button"><a href="#"><img src="images/sorting-icon.png" alt=""></a></div>
                    </div>
                </li>';
        }
    }
    echo $html;
}

function combine_chart($chartType = 'MSColumn3D', $outputArr = array(), $chartAttr = '', $axis_selector = '') {
    $xaxis = array();

    for ($i = 0; $i < count($outputArr); $i++) {
        if ($axis_selector['x_axis_select'] != '')
            $xaxis[$i] = $outputArr[$i][$axis_selector['x_axis_select']];
        else
            $xaxis[$i] = $outputArr[$i][0];

        $no_fields = (count($outputArr[$i]) / 2) - 1;

        if ($outputArr[$i] != array() || $outputArr[$i] != NULL)
            $keys = array_keys($outputArr[$i]);


        if (is_array($axis_selector['y1_axis_select']) && (count($axis_selector['y1_axis_select']) > 0)) {
            $no_fields = count($axis_selector['y1_axis_select']);
//            $y1_axis_select[] = explode(", ", $axis_selector['y1_axis_select']);
        } else {
            $no_fields = 1;
//            $y1_axis_select[] = trim($axis_selector['y1_axis_select']);
        }
        for ($k = 0; $k < $no_fields; $k++) {
//            $arrData[$k][0] = "Handle";
//            $arrData[$k][0] = $keys[(($k + 1) * 2) + 1];
            $arrData[$k][0] = $axis_selector['y1_axis_select'][$k];
            $arrData[$k][1] = "parentYAxis=S";
        }

        for ($j = 1; $j <= $no_fields; $j++) {
            $fan = $axis_selector['y1_axis_select'][$j - 1];
            $arrData[$j - 1][$i + 2] = $outputArr[$i][$fan];
        }
    }

    # Create combination chart object
//        $FC = new FusionCharts("MSCombiDY2D", "900", "300");
    $FC = new FusionCharts("$chartType", "100%", "300");
//    $FC->setRenderer('javascript');
    # Set Relative Path of swf file.
    $FC->setSWFPath("FusionCharts/");

    $chartAttr = str_replace(':', '=', $chartAttr);
    $chartAttr = str_replace("',", ";", $chartAttr);
    $chartAttr = trim(str_replace("'", "", $chartAttr));

    if ($axis_selector['y2_axis_lbl'] != '') {
        $chartAttr.="PYAxisName=" . $axis_selector['y1_axis_lbl'] . ";SYAXisName=" . $axis_selector['y2_axis_lbl'] . ";";
    } else {
        $chartAttr.="yaxisName=" . $axis_selector['y1_axis_lbl'] . ";";
    }

    # Define chart attributes
    $strParam = "caption=Chart;xAxisName=" . $axis_selector['x_axis_lbl'] . ";$chartAttr";

    # Set chart attributes
    $FC->setChartParams($strParam);

    # Pass the 2 arrays storing data and category names to
    # FusionCharts PHP Class function addChartDataFromArray
    $FC->addChartDataFromArray($arrData, array_values(array_unique($xaxis)));

    # Render the chart
    $variable = $FC->renderChart(FALSE, FALSE);
    return $variable;
}

function stacked_chart($chartType = 'StackedBar2D', $outputArr = array(), $chartAttr = '', $axis_selector = '', $fullstack = 0) {

    $datasetArr = array();
    $FC = new FusionCharts("$chartType", "100%", "300");
    $xaxis = array();
    for ($i = 0; $i < count($outputArr); $i++) {
        if ($axis_selector['x_axis_select'] != '' && $axis_selector['x_axis_select'] != null)
            $xaxis[$i] = $outputArr[$i][$axis_selector['x_axis_select']];
        else
            $xaxis[$i] = $outputArr[$i][0];

        if (is_array($axis_selector['y1_axis_select']) && (count($axis_selector['y1_axis_select']) > 0)) {
            $no_fields = count($axis_selector['y1_axis_select']);
        } else {
            $no_fields = 1;
        }
    }
    if (count($axis_selector['y1_axis_select']) > 0)
        foreach ($axis_selector['y1_axis_select'] as $series_names) {
            $FC->addDataset($series_names);
            for ($i = 0; $i < count($outputArr); $i++) {
                $FC->addChartData($outputArr[$i][$series_names]);
            }
        }

    $xaxis = array_values(array_unique($xaxis));

    ///////for adding X-axis category /////////////
    for ($l = 0; $l < count($xaxis); $l++) {
        $FC->addCategory($xaxis[$l]);
    }

//    if ((is_array($select_color)) && ($select_color != array())) {
//        $select_color = implode(";", $select_color);
//        if ($select_color != '')
//            $FC->addColors("$select_color");
//    }

    foreach (array_values(array_unique($datasetArr)) as $k => $v) {
        $FC->addDataset($v);
        for ($index = 0; $index < count($arrData); $index++) {
            $FC->addChartData($arrData[$k][$index]);
        }
    }

    # Set Relative Path of swf file.
    $FC->setSWFPath("FusionCharts/");

    $chartAttr = str_replace(':', '=', $chartAttr);
    $chartAttr = str_replace("',", ";", $chartAttr);
    $chartAttr = trim(str_replace("'", "", $chartAttr));

    if ($axis_selector['y2_axis_lbl'] != '') {
        $chartAttr.="PYAxisName=" . $axis_selector['y1_axis_lbl'] . ";SYAXisName=" . $axis_selector['y2_axis_lbl'] . ";";
    } else {
        $chartAttr.="yAxisName=" . $axis_selector['y1_axis_lbl'] . ";";
    }

    # Define chart attributes
    $strParam = "caption=Chart;PYAxisName=Lifetime;xAxisName=" . $axis_selector['x_axis_lbl'] . ";stack100Percent=$fullstack;$chartAttr";
    $FC->setChartParams($strParam);

//    $FC->setRenderer("javascript");
    $variable = $FC->renderChart(FALSE, FALSE);

    return $variable;
}

function pie_chart($chartType = 'Pie2D', $outputArr = array(), $chartAttr = '', $axis_selector = '') {

    $datasetArr = array();
    $FC = new FusionCharts("$chartType", "100%", "300");
    $xaxis = array();
    for ($i = 0; $i < count($outputArr); $i++) {
        if ($axis_selector['x_axis_select'] != '' && $axis_selector['x_axis_select'] != null)
            $xaxis[$i] = $outputArr[$i][$axis_selector['x_axis_select']];
        else
            $xaxis[$i] = $outputArr[$i][0];
//        $datasetArr[$i] = $outputArr[$i][1];
    }
    $xaxis = array_values(array_unique($xaxis));

//    if ((is_array($select_color)) && ($select_color != array())) {
//        $select_color = implode(";", $select_color);
//        if ($select_color != '')
//            $FC->addColors("$select_color");
//    }
    for ($index = 0; $index < count($outputArr); $index++) {
        $series_name = $axis_selector['y1_axis_select'][0];

        $FC->addChartData($outputArr[$index][$series_name], "label=" . $outputArr[$index][$axis_selector['x_axis_select']]);
    }
    # Set Relative Path of swf file.
    $FC->setSWFPath("FusionCharts/");

    $chartAttr = str_replace(':', '=', $chartAttr);
    $chartAttr = str_replace("',", ";", $chartAttr);
    $chartAttr = trim(str_replace("'", "", $chartAttr));

    if ($axis_selector['y2_axis_lbl'] != '') {
        $chartAttr.="PYAxisName=" . $axis_selector['y1_axis_lbl'] . ";SYAXisName=" . $axis_selector['y2_axis_lbl'] . ";";
    } else {
        $chartAttr.="yAxisName=" . $axis_selector['y1_axis_lbl'] . ";";
    }

    # Define chart attributes
    $strParam = "caption=Chart;xAxisName=" . $axis_selector['x_axis_lbl'] . ";$chartAttr";
    $FC->setChartParams($strParam);

    $variable = $FC->renderChart(FALSE, FALSE);
    return $variable;
}

function multiYaxis_chart($chartType = 'MSCombiDY2D', $outputArr = array(), $chartAttr = '', $axis_selector = '') {

    $datasetArr = array();

    $FC = new FusionCharts("$chartType", "100%", "300");
    # Set Relative Path of swf file.
    $FC->setSWFPath("FusionCharts/");

    //////////////////////////    Chart Customization Attribute - parsing Start ///////////////////////////////////////
    $chartAttr = (json_decode(stripcslashes($chartAttr), true));
    $xaxisAttr = '';
    if (isset($chartAttr['xAxis']) && count($chartAttr['xAxis']) > 0) {
        foreach (($chartAttr['xAxis'][0]) as $xKeyAttr => $xValAttr) {
            $xaxisAttr.= $xKeyAttr . "=" . $xValAttr . ";";
        }
    }
    $FC->setCategoriesParams($xaxisAttr);

    $y1axisAttr = array();
    if (isset($chartAttr['y1Axis']) && count($chartAttr['y1Axis']) > 0) {
        $i = 0;
        foreach (($chartAttr['y1Axis']) as $val) {
            $y1axis = '';
            foreach ($val as $y1KeyAttr => $y1ValAttr) {
                $y1axis.= $y1KeyAttr . "=" . $y1ValAttr . ";";
            }
            $y1axisAttr[$i] = $y1axis;
            $i++;
        }
    }
    $y2axisAttr = array();
    if (isset($chartAttr['y2Axis']) && count($chartAttr['y2Axis']) > 0) {
        $j = 0;
        foreach (($chartAttr['y2Axis']) as $val) {
            $y2axis = '';
            foreach ($val as $y2KeyAttr => $y2ValAttr) {
                $y2axis.= $y2KeyAttr . "=" . $y2ValAttr . ";";
            }
            $y2axisAttr[$j] = $y2axis;
            $j++;
        }
    }

    $chartaxisAttr = '';
    if (isset($chartAttr['chart']) && count($chartAttr['chart']) > 0) {
        foreach (($chartAttr['chart']) as $chartKeyAttr => $chartValAttr) {
            $chartaxisAttr.= $chartKeyAttr . "=" . $chartValAttr . ";";
        }
    }
    //////////////////////////    Chart Customization Attribute - parsing End ///////////////////////////////////////

    
//    $chartAttr = str_replace(':', '=', $chartAttr);
//    $chartAttr = str_replace("',", ";", $chartAttr);
//    $chartAttr = trim(str_replace("'", "", $chartAttr));

    if ($axis_selector['y2_axis_lbl'] != '') {
        $chartaxisAttr.="PYAxisName=" . $axis_selector['y1_axis_lbl'] . ";SYAXisName=" . $axis_selector['y2_axis_lbl'] . ";";
    } else {
        $chartaxisAttr.="yAxisName=" . $axis_selector['y1_axis_lbl'] . ";";
    }

    # Define chart attributes
    $strParam = "caption=Chart;xAxisName=" . $axis_selector['x_axis_lbl'] . ";$chartaxisAttr";
//    $strParam = "caption=Chart;xAxisName=;plotGradientColor=ff00ff;xaxisname=Post Types;pyaxisname=Volume;syaxisname=EPM;decimals=0;numbersuffix=K;snumbersuffix=%;setadaptivesymin=1;showplotborder=1;yAxisName=;";

    $FC->setChartParams($strParam);

    $xaxis = array();
    for ($i = 0; $i < count($outputArr); $i++) {
        if ($axis_selector['x_axis_select'] != '' && $axis_selector['x_axis_select'] != null) {
            $FC->addCategory($outputArr[$i][$axis_selector['x_axis_select']]);
        } else {
            $FC->addCategory($outputArr[$i][0]);
        }
    }

    if ($chartType == "MSStackedColumn2DLineDY") {
        $FC->createMSStDataset();
        for ($index = 0; $index < count($axis_selector['y1_axis_select']); $index++) {
            $series_name = $axis_selector['y1_axis_select'][$index];
            $FC->addMSStSubDataset("$series_name", "showValues=0;" . $y1axisAttr[$index]);
            for ($k = 0; $k < count($outputArr); $k++) {
                $FC->addChartData($outputArr[$k][$series_name]);
            }
        }

        $FC->createMSStDataset();
        for ($index = 0; $index < count($axis_selector['y2_axis_select']); $index++) {
            $y2_series_name = $axis_selector['y2_axis_select'][$index];

            $FC->addMSLineset("$y2_series_name", "parentYAxis=S;" . $y2axisAttr[$index]);
            for ($k = 0; $k < count($outputArr); $k++) {
                if ($outputArr[$k][$y2_series_name] != '')
                    $FC->addMSLinesetData($outputArr[$k][$y2_series_name]);
            }
        }
    } else {
        for ($index = 0; $index < count($axis_selector['y1_axis_select']); $index++) {
            $series_name = $axis_selector['y1_axis_select'][$index];
//        $FC->addChartData($outputArr[$index][$series_name], "label=" . $series_name);
            $FC->addDataset("$series_name", "showValues=0;" . $y1axisAttr[$index]);
            for ($k = 0; $k < count($outputArr); $k++) {
                $FC->addChartData($outputArr[$k][$series_name]);
            }
        }

        for ($index = 0; $index < count($axis_selector['y2_axis_select']); $index++) {
            $y2_series_name = $axis_selector['y2_axis_select'][$index];

//        $FC->addChartData($outputArr[$index][$series_name], "label=" . $series_name);
            $FC->addDataset("$y2_series_name", "parentYAxis=S;" . $y2axisAttr[$index]);
            for ($k = 0; $k < count($outputArr); $k++) {
                $FC->addChartData($outputArr[$k][$y2_series_name]);
            }
        }
    }
    $variable = $FC->renderChart(FALSE, FALSE);
    return $variable;
}

function scatter_chart($chartType = 'Scatter', $outputArr = array(), $chartAttr = '', $axis_selector = '') {
    $FC = new FusionCharts("$chartType", "100%", "300");
    $FC->setSWFPath("FusionCharts/");
//      Bubble
//    $xaxis = array();
//    for ($i = 0; $i < count($outputArr); $i++) {
//        if ($axis_selector['x_axis_select'] != '' && $axis_selector['x_axis_select'] != null)
//            $xaxis[$i] = $outputArr[$i][$axis_selector['x_axis_select']];
//        else
//            $xaxis[$i] = $outputArr[$i][0];
//    }
//    $xaxis = array_values(array_unique($xaxis));
    $xseries = $axis_selector['x_axis_select'];

    if ($chartType == 'Scatter') {
        $FC->addDataSet($xseries, "anchorRadius=7");
    } else {
        $FC->addDataSet($xseries);
    }
    $yseries_name = $axis_selector['y1_axis_select'][0];
    $zseries_name = $axis_selector['y2_axis_select'][0];

    for ($index = 0; $index < count($outputArr); $index++) {
        if ($chartType == 'Scatter') {
            $FC->addChartData($outputArr[$index][$xseries], "y=" . $outputArr[$index][$yseries_name]);
        } else {
            $FC->addChartData($outputArr[$index][$xseries], "y=" . $outputArr[$index][$yseries_name] . ";z=" . (($outputArr[$index][$zseries_name] <= 0) ? 5 : $outputArr[$index][$zseries_name]));
        }
    }
    $chartAttr = str_replace(':', '=', $chartAttr);
    $chartAttr = str_replace("',", ";", $chartAttr);
    $chartAttr = trim(str_replace("'", "", $chartAttr));

    if ($axis_selector['y2_axis_lbl'] != '') {
        $chartAttr.="PYAxisName=" . $axis_selector['y1_axis_lbl'] . ";SYAXisName=" . $axis_selector['y2_axis_lbl'] . ";";
    } else {
        $chartAttr.="yAxisName=" . $axis_selector['y1_axis_lbl'] . ";";
    }

    # Define chart attributes
    $strParam = "caption=Chart;xAxisName=" . $axis_selector['x_axis_lbl'] . ";$chartAttr";

    $FC->setChartParams($strParam);

    $variable = $FC->renderChart(FALSE, FALSE);
    return $variable;
}

function getChartVariable($chartType, $outputArr, $chartAttr, $axis_selector) {

    if ($chartType == "Pie2D" || $chartType == "Pie3D" || $chartType == "Column3D" || $chartType == "Column2D" || $chartType == "Line" || $chartType == "Area2D" || $chartType == "Bar2D" || $chartType == "Doughnut2D" || $chartType == "Doughnut3D" || $chartType == "Pareto2D" || $chartType == "Pareto3D") {
        $variable = pie_chart($chartType, $outputArr, $chartAttr, $axis_selector);
    } elseif ($chartType == "MSColumn3D" || $chartType == "MSArea" || $chartType == "MSBar2D" || $chartType == "MSBar3D" || $chartType == "MSColumn2D" || $chartType == "MSCombi2D" || $chartType == "MSCombi3D" || $chartType == "MSLine" || $chartType == "Marimekko" || $chartType == "ZoomLine" || $chartType == "MSColumnLine3D" || $chartType == "StackedColumn2DLine" || $chartType == "StackedColumn3DLine") {
        /////// for Combine Chart ----from array
        $variable = combine_chart($chartType, $outputArr, $chartAttr, $axis_selector);
    } elseif ($chartType == "StackedColumn3D" || $chartType == "StackedColumn2D" || $chartType == "StackedBar2D" || $chartType == "StackedBar3D" || $chartType == "StackedArea2D" || $chartType == "MSStackedColumn2D") {
        $variable = stacked_chart($chartType, $outputArr, $chartAttr, $axis_selector, 0);
    } elseif ($chartType == "StackedColumn3D_1" || $chartType == "StackedColumn2D_1" || $chartType == "StackedBar2D_1" || $chartType == "StackedBar3D_1") {
        $chartTypeArr = explode("_", $chartType);
        $chartType = $chartTypeArr[0];
        $variable = stacked_chart($chartType, $outputArr, $chartAttr, $axis_selector, 1);
    } elseif ($chartType == "MSCombiDY2D" || $chartType == "MSColumn3DLineDY" || $chartType == "StackedColumn3DLineDY" || $chartType == "MSStackedColumn2DLineDY" || $chartType == "ScrollCombiDY2D") {
        $variable = multiYaxis_chart($chartType, $outputArr, $chartAttr, $axis_selector);
    } elseif ($chartType == "Scatter" || $chartType == "Bubble") {
        $variable = scatter_chart($chartType, $outputArr, $chartAttr, $axis_selector);
    }
    return $variable;
}

mysql_close($conn);
?>