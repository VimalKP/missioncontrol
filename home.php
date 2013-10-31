<?php include_once 'header.php'; ?>

<?php
global $channels;
$globalArr = array();
$totalPosts = 0;
$totalUsers = 0;
$totalEngagements = 0;
$totalBrands = 0;
$i = 0;
//$query1 = "SELECT SUM(`posts`) as posts,SUM(`engagements`) as engagements,count(*) as brands FROM %s.brands";
//$query2 = "SELECT count(*) as users FROM %s.users";
//$query3 = "SELECT MAX(date) as last_date FROM %s.historical";


foreach ($channels as $channel => $dbName) {
//    echo sprintf($query1, $dbName)."<br/>";

    $query1 = "SELECT SUM(`posts`) as posts,SUM(`engagements`) as engagements,count(*) as brands,SUM(`engagedUser`) as users FROM $dbName.brands";
    $result1 = select("$query1");
    $globalArr[$i]['channel'] = $channel;
    $globalArr[$i]['posts'] = $result1[0]['posts'];
    $globalArr[$i]['engagements'] = $result1[0]['engagements'];
    $globalArr[$i]['brands'] = $result1[0]['brands'];
    $totalPosts+=$result1[0]['posts'];
    $totalEngagements+=$result1[0]['engagements'];
    $totalBrands+=$result1[0]['brands'];
//    $query2 = "SELECT count(*) as users FROM $dbName.users";
//    $result2 = select("$query2");
    $globalArr[$i]['users'] = $result1[0]['users'];
    $totalUsers+=$result1[0]['users'];
    $query3 = "SELECT MAX(date) as last_date FROM $dbName.historical";
    $result3 = select("$query3");
    $globalArr[$i]['last_date'] = $result3[0]['last_date'];
    $i++;
}
?>
<script type="text/javascript">
   
    $(document).ready(function() {
        //Default Action
        //        $(".tab_content").hide(); //Hide all content
        $("#mission_controlpage").addClass("selected"); //Activate first tab
    });
</script>
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
