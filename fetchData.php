
<tr>
    <th width="20"  scope="col">Q_ID</th>
    <th scope="col">Channel</th>
    <th scope="col">Question</th>
    <th scope="col">Query</th>
    <th scope="col">url</th>
</tr>

<?php
include_once 'db_connection.php';
$targetpage = "test.php";
$limit = 3;     //limit of dispaly

$query = "SELECT * from questions WHERE passed=0";
$total_pages = mysql_num_rows(mysql_query($query));
//            $total_pages = $total_pages[num];
$stages = 3;
$page = mysql_escape_string($_POST['page']);
if ($page) {
    $start = ($page - 1) * $limit;
} else {
    $start = 0;
}

// Get page data
$sel = mysql_query("SELECT * from questions WHERE passed=0 LIMIT $start, $limit");
while ($row1 = mysql_fetch_array($sel)) {
    $id = $row1['qa_id'];
    $name = $row1['channel'];
    $doj = $row1['question'];
    $experince = $row1['query'];
    $salary = $row1['url'];
    ?>
    <tr >
        <td height="20"><?php echo $id; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $doj; ?></td>
        <td><?php echo $experince ?>year</td>
        <td><?php echo $salary ?></td>

    </tr>
<?php } ?>
<tr>
    <td colspan="5" >
        <?php
        // Initial page num setup
        if ($page == 0) {
            $page = 1;
        }
        $prev = $page - 1;
        $next = $page + 1;
        $lastpage = ceil($total_pages / $limit);
        $LastPagem1 = $lastpage - 1;
        $paginate = '';
        if ($lastpage > 1) {

            $paginate .= "<div class='paginate' >";
            // First
            if ($page > 1) {
                $paginate.= "<a href='#' onclick='getPageData(1)'> << </a>";
            } else {
                $paginate.= "<span class='disabled'><</span>";
            }
            
            // Previous
            if ($page > 1) {
                $paginate.= "<a href='#' onclick='getPageData($prev)'> < </a>";
            } else {
                $paginate.= "<span class='disabled'><</span>";
            }
            
            // Pages	
            if ($lastpage < 7 + ($stages * 2)) { // Not enough pages to breaking it up
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $paginate.= "<span class='current'>$counter</span>";
                    } else {
                        $paginate.= "<a href='#' onclick='getPageData($counter)'>$counter</a>";
                    }
                }
            } elseif ($lastpage > 5 + ($stages * 2)) { // Enough pages to hide a few?
                // Beginning only hide later pages
                if ($page < 1 + ($stages * 2)) {
                    for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
                        if ($counter == $page) {
                            $paginate.= "<span class='current'>$counter</span>";
                        } else {
                            $paginate.= "<a href='#' onclick='getPageData($counter)'>$counter</a>";
                        }
                    }
                    $paginate.= "...";
                    $paginate.= "<a href='#' onclick='getPageData($LastPagem1)'>$LastPagem1</a>";
                    $paginate.= "<a href='#' onclick='getPageData($lastpage)'>$lastpage</a>";
                }
                // Middle hide some front and some back
                elseif ($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {
                    $paginate.= "<a href='#' onclick='getPageData(1)'>1</a>";
                    $paginate.= "<a href='#' onclick='getPageData(2)'>2</a>";
                    $paginate.= "...";
                    for ($counter = $page - $stages; $counter <= $page + $stages; $counter++) {
                        if ($counter == $page) {
                            $paginate.= "<span class='current'>$counter</span>";
                        } else {
                            $paginate.= "<a href='#' onclick='getPageData($counter)'>$counter</a>";
                        }
                    }
                    $paginate.= "...";
                    $paginate.= "<a href='#' onclick='getPageData($LastPagem1)'>$LastPagem1</a>";
                    $paginate.= "<a href='#' onclick='getPageData($lastpage)'>$lastpage</a>";
                }
                // End only hide early pages
                else {
                    $paginate.= "<a href='#' onclick='getPageData(1)'>1</a>";
                    $paginate.= "<a href='#' onclick='getPageData(2)'>2</a>";
                    $paginate.= "...";
                    for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page) {
                            $paginate.= "<span class='current'>$counter</span>";
                        } else {
                            $paginate.= "<a href='#' onclick='getPageData($counter)'>$counter</a>";
                        }
                    }
                }
            }

            // Next
            if ($page < $counter - 1) {
                $paginate.= "<a href='#' onclick='getPageData($next)'> > </a>";
            } else {
                $paginate.= "<span class='disabled'>></span>";
            }
            
            // Last
            if ($page < $counter - 1) {
                $paginate.= "<a href='#' onclick='getPageData($lastpage)'> >> </a>";
            } else {
                $paginate.= "<span class='disabled'>></span>";
            }
            $paginate.= "</div>";
        }
        echo $total_pages . ' Results';
        // pagination
        echo $paginate;
        ?>
    </td>
</tr>
