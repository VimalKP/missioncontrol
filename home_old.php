<?php include_once 'header.php'; ?>
<script type="text/javascript">
   
    $(document).ready(function() {
        //Default Action
        //        $(".tab_content").hide(); //Hide all content
        $("#mission_controlpage").addClass("selected"); //Activate first tab
    });
</script>
<?php
global $channels;
$query1 = "SELECT SUM(`posts`) as posts,SUM(`engagements`) as engagements,count(*) as brands FROM %s.brands";
$query2 = "SELECT count(*) as users FROM %s.users";
$query3 = "SELECT MAX(date) as last_date FROM %s.historical";

//function numberconvert($no) {
//    if ($no >= 1000 && $no < 1000000) {
//        return round($no / 1000) . "K";
//    } elseif ($no >= 1000000) {
//        return round($no / 1000000) . "M";
//    } else {
//        return $no;
//    }
//}
?>
<div class="tab_container">
    <div id="tab1" class="tab_content" style="">
        <h4 style="margin-left: 9px;">Database Overview</h4>
        <table class="table table-striped table-bordered" id="example">
            <thead>
                <tr>
                    <th>Channels</th>
                    <th>Tracked Accounts</th>
                    <th>Users</th>
                    <th>Posts</th>
                    <th>Engagements</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $globalArr = array();
                $totalPosts = 0;
                $totalUsers = 0;
                $totalEngagements = 0;
                $totalBrands = 0;
                $i = 0;
                foreach ($channels as $channel => $dbName) {
//    echo sprintf($query1, $dbName)."<br/>";

                    $globalArr[$i]['channel'] = $channel;

                    $q = mysql_query(sprintf($query1, $dbName));
                    while ($row = mysql_fetch_assoc($q)) {
                        $globalArr[$i]['posts'] = $row['posts'];
                        $globalArr[$i]['engagements'] = $row['engagements'];
                        $globalArr[$i]['brands'] = $row['brands'];

                        $totalPosts+=$row['posts'];
                        $totalEngagements+=$row['engagements'];
                        $totalBrands+=$row['brands'];
                    }
                    $qu = mysql_query(sprintf($query2, $dbName));
                    $user = mysql_fetch_assoc($qu);
                    $globalArr[$i]['users'] = $user['users'];

                    $que = mysql_query(sprintf($query3, $dbName));
                    $lastDate = mysql_fetch_assoc($que);
                    $globalArr[$i]['last_date'] = $lastDate['last_date'];

                    $totalUsers+=$user['users'];
                    $i++;
                }
                foreach ($globalArr as $key => $value) {
                    echo "<tr><td><a class='module_title_a' href='managepages.php?pagename=" . $value['channel'] . "'>" . ucfirst($value['channel']) . "</a></td>";
                    echo "<td>" . numberconvert($value['brands']) . "</td>";
                    echo "<td>" . numberconvert($value['users']) . "</td>";
                    echo "<td>" . numberconvert($value['posts']) . "</td>";
                    echo "<td>" . numberconvert($value['engagements']) . "</td>";
                    echo "<td>" . ($value['last_date']) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <table class="table table-striped table-bordered" style="margin-top: -7px;">
            <tr>
                <td style="width: 134px;"><b>Total</b></td>
                <td style="width: 239px;"><b><?= numberconvert($totalBrands) ?></b></td>
                <td style="width: 88px;"><b><?= numberconvert($totalUsers) ?></b></td>
                <td style="width: 84px;"><b><?= numberconvert($totalPosts) ?></b></td>
                <td style="width: 188px;"><b><?= numberconvert($totalEngagements) ?></b></td>
                <td style="width: 181px;">  --  </td>
            </tr>
        </table>
    </div>
    <?php include_once 'footer.php'; ?>
</body>
</html>
