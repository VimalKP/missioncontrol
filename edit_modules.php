<?php
include_once 'header.php';
include("includes/FusionCharts_Gen.php");
global $channels;
?>
<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.13.custom.css">
<!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">
<link href="css/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="css/daterangepicker-bs2.css" />

<!--<script src="js/jquery-ui-1.8.13.custom.min.js"></script>-->
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/daterangepicker.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="js/ui.dropdownchecklist-1.4-min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/easySlider1.7.js"></script>
<script LANGUAGE="Javascript" SRC="js/FusionCharts.js"></script>
<script LANGUAGE="Javascript" SRC="js/FusionCharts.HC.js"></script>
<script LANGUAGE="Javascript" SRC="js/FusionCharts.HC.Charts.js"></script>
<style type="text/css">
    li {
        list-style:none;
        height: 29px;        
    }
    .overlay-bg {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height:100%;
        width: 100%;
        background: #000; /* fallback */
        background: rgba(0,0,0,0.75);
    }
    .overlay-content {
        background: #fff;
        padding: 1%;
        width: 210px;;
        position: relative;
        top: 40%;
        left: 63%;
        margin: 0 0 0 -20%; /* add negative left margin for half the width to center the div */
        border-radius: 4px;
        box-shadow: 0 0 5px rgba(0,0,0,0.9);
    }

    .close-btn {
        cursor: pointer;
        border: 1px solid #333;
        padding: 2% 5%;
        background: #a9e7f9; /* fallback */
        background: -moz-linear-gradient(top,  #a9e7f9 0%, #77d3ef 4%, #05abe0 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#a9e7f9), color-stop(4%,#77d3ef), color-stop(100%,#05abe0));
        background: -webkit-linear-gradient(top,  #a9e7f9 0%,#77d3ef 4%,#05abe0 100%);
        background: -o-linear-gradient(top,  #a9e7f9 0%,#77d3ef 4%,#05abe0 100%);
        background: -ms-linear-gradient(top,  #a9e7f9 0%,#77d3ef 4%,#05abe0 100%);
        background: linear-gradient(to bottom,  #a9e7f9 0%,#77d3ef 4%,#05abe0 100%);
        border-radius: 4px;
        box-shadow: 0 0 4px rgba(0,0,0,0.3);
    }
    .close-btn:hover {
        background: #05abe0;
    }
</style>

<script type="text/javascript">
    $(document).ready(function(){
        $(".graph-div").hide();
        
        $("#module_page").addClass("selected"); //Activate first tab
        
        $("#slider").easySlider();
        
        $("#select_module").dropdownchecklist({maxDropHeight: 150, onComplete: function(selector) {    
                $("#select_competitor option").removeAttr("disabled");
                for( i=0; i < selector.options.length; i++ ) {
                    if (selector.options[i].selected && (selector.options[i].value != "")) {
                        $("#select_competitor").children('option').each(function() {
                            if ( $(this).val() === selector.options[i].value ) {
                                $("#select_competitor option").eq(i).removeAttr("selected").attr("disabled", "disabled"); 
                                $("#select_competitor").dropdownchecklist("refresh");
                            }
                        });
                    }
                }
                drawChart();
            }
        });
        $("#select_competitor").dropdownchecklist({maxDropHeight: 150,
            onComplete: function(selector) {
                drawChart();
            }
        });
        $("#y1_axis_select").dropdownchecklist({maxDropHeight: 150,width:500,
            onComplete: function(selector) {
                redrawChart();
            }
        });
        $("#y2_axis_select").dropdownchecklist({maxDropHeight: 150,
            onComplete: function(selector) {
                redrawChart();
            }
        });
        
        fillModule($("#channel").val());   
        
        $("#takeaways_sortable").sortable({
            update: function(event, ui) {
                              
                var tkIds = new Array();
                var i = 0;
                var values=$('input[name="takeaways_sortorder[]"]').map(function(){
                    tkIds[i] = $(this).val();
                    i++;
                });
                update_takeaway_sortorder(tkIds);
            }
        });
        
        $("#channel").live('change',function(){
            $("#page_breadcrumb").html($(this).val());
            $("#page_breadcrumb").attr("href","modules.php?page="+$(this).val());
            fillModule($(this).val());
        });
        $("#objective").live('change',function(){
            $("#section_breadcrumb").html($(this).val());
            $("#section_breadcrumb").attr("href","modules.php?page="+$("#channel").val()+"&section="+$(this).val());
        });
        
        $("#x_axis_select").live('change',function(){
            redrawChart();
        });
        $("#chartType").live('change',function(){
            var chartType=$(this).val();
            $("#y2axis_div").hide();
            $("#y2axis_lbl").html('Y2 Axis');
            $("#y1axis_lbl").html('Y1 Axis');
            if(chartType=="MSCombiDY2D" || chartType=="MSColumn3DLineDY" || chartType=="StackedColumn3DLineDY" || chartType=="MSStackedColumn2DLineDY" || chartType=="ScrollCombiDY2D"){
                $("#y2axis_div").show();
            }
            if(chartType=='Bubble'){
                $("#y2axis_lbl").html('Z Value');
                $("#y1axis_lbl").html('Y Value');
                $("#y2axis_div").show();
            }
            redrawChart();
        });
        
        $( "li" ).disableSelection();
        $('#dateTB').daterangepicker({
            format: 'YYYY-MM-DD'
        });
        
        $(".applyBtn").live('click',function(){
            drawChart();
        })
    });	
    function popuptext(text,delay,fadetime,callback){
        delay=typeof(delay)!='undefined'?delay:1000;
        fadetime=typeof(fadetime)!='undefined'?fadetime:1000;
        var close_button=$('<div class="rounded_button" style="background:#F73E5C;color:white;float:right;">Close</div>');

        var popup=$('<div class="transparentpopup" id="transpopup">'+text+'<div style="clear:both;padding-top:25px;"></div></div>');

        popup.append(close_button);
        $('body').prepend(popup);
        
        close_button.bind('click',function(){popup.remove();});
        div_position('transpopup');
        if (!$.browser.msie) {popup.css('position','fixed');}
        setTimeout(function(){popup.fadeOut(fadetime,function(){popup.remove(); if(typeof(callback)=='function'){callback.call(this)} });},delay)
    }
    
    function fillModule(channelName){
        var moduleId=$("#moduleId").val();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            async:false,
            data: {
                'action':'fillModule',
                'channelName':channelName,
                'moduleId':moduleId
            },
            success:function(data){
                var obj=$.parseJSON(data);
                //                console.log(obj.y1_axis_select)
                $("#chartType").val(obj.chartType);
                
                $("#x_axis_select").val(obj.x_axis_select);
                $("#y1_axis_select").val(obj.y1_axis_select);

                $("#y2axis_div").hide();
                if(obj.y2_axis_select!='')
                {
                    $("#y2axis_div").show();
                }
                $("#y2_axis_select").val(obj.y2_axis_select);
                $("#x_axis_lbl").val(obj.x_axis_lbl);
                $("#y1_axis_lbl").val(obj.y1_axis_lbl);
                $("#y2_axis_lbl").val(obj.y2_axis_lbl);
                
                $("#select_competitor").dropdownchecklist("destroy");
                $("#select_competitor").html(obj.competitor);
                
                $("#select_module").dropdownchecklist("destroy");
                $("#select_module").html(obj.brand);
                $("#select_competitor").dropdownchecklist({maxDropHeight: 150,
                    onComplete: function(selector) {
                        drawChart();
                    }            
                });
                $("#select_module").dropdownchecklist({maxDropHeight: 150, onComplete: function(selector) {
                        $("#select_competitor option").removeAttr("disabled");
                        for( i=0; i < selector.options.length; i++ ) {
                            if (selector.options[i].selected && (selector.options[i].value != "")) {
                                $("#select_competitor").children('option').each(function() {
                                    if ( $(this).val() === selector.options[i].value ) {
                                        $("#select_competitor option").eq(i).removeAttr("selected").attr("disabled", "disabled");
                                        $("#select_competitor").dropdownchecklist("refresh");
                                    }
                                });
                            }
                        }
                        drawChart();
                    }
                });
                
                var j=0;
                if(($("#dateTB").val())!='' && $("#select_module").val()!=''){
                    drawChart();
                }
            }
        });  
    }
    
    function addTakeaways(){
        var question=$("#tk_question").val();
        var query=$("#tk_query").val();
        
        var brandIds=($("#select_module").val());
        var date=($("#dateTB").val());
        if(question==''){
            $("#tk_question").focus();
            return false;
        }
        if(query==''){
            $("#tk_query").focus();
            return false;
        }
        var moduleId=$("#moduleId").val();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'addtakeaway',
                'question':question,
                'query':query,
                'brandIds':brandIds,
                'date':date,
                'moduleId':moduleId
            },
            success:function(data){
                $('#myModal').modal('hide');
                //                console.log(data)
                $("#takeaways_sortable").html(data);
                $("#tk_cnt").html(parseInt($("#tk_cnt").html())+1);
            }
        });  
        
    }
    
    function editTakeaways(){
        var question=$("#edtk_question").val();
        var query=$("#edtk_query").val();
          
        var brandIds=($("#select_module").val());
        var date=($("#dateTB").val());
        var moduleId=$("#moduleId").val();
        var tkId=$("#editId").val();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'edittakeaway',
                'question':question,
                'query':query,
                'moduleId':moduleId,
                'brandIds':brandIds,
                'date':date,
                'takeawayId':tkId
            },
            success:function(data){
                $('#editModal').modal('hide')
                //                console.log(data)
                $("#takeaways_sortable").html(data);
                
            }
        });  
        
    }
    function deleteTakeaways(){
        var moduleId=$("#moduleId").val();
        var tk_id=$("#delId").val();
        
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'deletetakeaway',
                'tk_id':tk_id,
                'moduleId':moduleId
            },
            success:function(data){
                $('#delModal').modal('hide');
                $("#takeaways_sortable").html(data);
                $("#tk_cnt").html(parseInt($("#tk_cnt").html())-1);
            }
        });  
        
    }
    function update_takeaway_sortorder(tkIds){
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'updateSortOrder',
                'tkIds':tkIds
            },
            success:function(data){
                $("#popup_msg").html('Sucessfully Sorted Data!');
                $('.overlay-bg').fadeIn(500);
                $(".overlay-bg").fadeOut(800);
            }
        });
    }
    function updateModuleData(){
        $("#selected_pages").val($("#ddcl-select_module").text());
        $("#selected_competitor").val($("#ddcl-select_competitor").text());
        //        $("#selected_colors").val($("#ddcl-select_color").text());
//        var result_query=$("#result_query").text();
        var datas=$("#modulefrm").serialize();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: datas,
            success:function(data){
                drawChart();
                $("#popup_msg").html('Sucessfully Saved Module Data!');
                $('.overlay-bg').fadeIn(500);
                $(".overlay-bg").fadeOut(800);
                    
            }
        });  
    }
    function drawChart(){
        var moduleId=($("#moduleId").val());
        var brandIds=($("#select_module").val());
        var competitor=($("#select_competitor").val());

        var x_axis_select=$("#x_axis_select").val();
        var y1_axis_select=$("#y1_axis_select").val();
        var y2_axis_select=$("#y2_axis_select").val();
        var x_axis_lbl=$("#x_axis_lbl").val();
        var y1_axis_lbl=$("#y1_axis_lbl").val();
        var y2_axis_lbl=$("#y2_axis_lbl").val();

        //        var select_color=($("#select_color").val());
        var date=($("#dateTB").val());
        var chartType=$("#chartType").val();
        //        if(chartType!='')
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'drawChart',
                'moduleId':moduleId,
                'brandIds':brandIds,
                'competitor':competitor,
                'x_axis_select':x_axis_select,
                'y1_axis_select':y1_axis_select,
                'y2_axis_select':y2_axis_select,
                'x_axis_lbl':x_axis_lbl,
                'y1_axis_lbl':y1_axis_lbl,
                'y2_axis_lbl':y2_axis_lbl,
                'dates':date,
                'chartType':chartType
                //                'select_color':select_color
            },
            async:false,
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                var obj=$.parseJSON(data);
                //                console.log(obj.chart);
                
                $("#myChart").html(obj.chart);
                $("#query_output").val(obj.query_output);
                if(obj.result_query==''){
                    $("#result_query_div").hide();
                }else{
                    $("#result_query").html("<pre style='text-transform:none;background: none repeat scroll 0% 0% transparent ! important; width: 95%; border: medium none ! important;'>"+obj.result_query+"</pre>");
                    $("#result_query_div").show();
                }
                
                $("#x_axis_select").html(obj.fields);
                
                $("#y2_axis_select").dropdownchecklist("destroy");
                $("#y2_axis_select").html("<option value=''>None</option>"+obj.fields);
                
                $("#x_axis_select").val(x_axis_select);
                if(x_axis_select=='' || x_axis_select==null){
                    $("#x_axis_select").val(obj.x_axis_select);
                }
                
                $("#y2axis_div").hide();      
                $("#y2axis_lbl").html('Y2 Axis');
                $("#y1axis_lbl").html('Y1 Axis');
                if(chartType=="MSCombiDY2D" || chartType=="MSColumn3DLineDY" || chartType=="StackedColumn3DLineDY" || chartType=="MSStackedColumn2DLineDY" || chartType=="ScrollCombiDY2D"){
                    $("#y2axis_div").show();
                }
                if(chartType=='Bubble'){
                    $("#y2axis_div").show();
                    $("#y2axis_lbl").html('Z Value');
                    $("#y1axis_lbl").html('Y Value');
                }
                
                
                $("#y1_axis_select").dropdownchecklist("destroy");
                $("#y1_axis_select").html(obj.fields);
                $("#y1_axis_select").val(obj.y1_axis_select);
                $("#y1_axis_select").dropdownchecklist({maxDropHeight: 150,width:500,
                    onComplete: function(selector) {
                        redrawChart();
                    }
                });
                $("#y2_axis_select").val(obj.y2_axis_select);
                $("#y2_axis_select").dropdownchecklist({maxDropHeight: 150,
                    onComplete: function(selector) {
                        redrawChart();
                    }
                });
                
                $("#x_axis_lbl").val(obj.x_axis_lbl);
                $("#y1_axis_lbl").val(obj.y1_axis_lbl);
                $("#y2_axis_lbl").val(obj.y2_axis_lbl);
                
                $("#chart_name").html($("#name_module").val());
                $("#chart_query").html($("#question").val());
                
                $(".graph-div").show();
                unoloaded();
                //                var obj =$.parseJSON(data);
                //                console.log(obj.dateArr);
                //                console.log(obj.chartArr);
                //                $("#popup_msg").html('Sucessfully Saved Module Data!');
                //                $('.overlay-bg').fadeIn(500);
                //                $(".overlay-bg").fadeOut(800);
            }
        });
    }
    function get_tkaw(takeId){
        $("#editId").val(takeId);
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'getTakeaway',
                'takeId':takeId
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                var obj=$.parseJSON(data);
                //                console.log(obj.takeaway_question);
                //                console.log(obj.query);
                
                $('#editModal').find('#edtk_question').val(obj.takeaway_question);
                $('#editModal').find('#edtk_query').val(obj.query);
                $('#editModal').modal('show');
                unoloaded();
            }
        });
    }
    function redrawChart(){
        var x_axis_select=$("#x_axis_select").val();
        var y1_axis_select=$("#y1_axis_select").val();
        var y2_axis_select=$("#y2_axis_select").val();
        var x_axis_lbl=$("#x_axis_lbl").val();
        var y1_axis_lbl=$("#y1_axis_lbl").val();
        var y2_axis_lbl=$("#y2_axis_lbl").val();
        var chartType=$("#chartType").val();
        var moduleId=$("#moduleId").val();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'redrawChart',
                'x_axis_select':x_axis_select,
                'y1_axis_select':y1_axis_select,
                'y2_axis_select':y2_axis_select,
                'x_axis_lbl':x_axis_lbl,
                'y1_axis_lbl':y1_axis_lbl,
                'y2_axis_lbl':y2_axis_lbl,
                'chartType':chartType,
                'moduleId':moduleId
            },
            async:false,
             beforeSend: function(){
                unoload();
            },
            success:function(data){
                var obj=$.parseJSON(data);
                $("#myChart").html(obj.chart);
                
                $("#x_axis_select").html(obj.fields);
                
                $("#y2_axis_select").dropdownchecklist("destroy");
                $("#y2_axis_select").html("<option value=''>None</option>"+obj.fields);
                
                $("#x_axis_select").val(x_axis_select);
                if(x_axis_select=='' || x_axis_select==null){
                    $("#x_axis_select").val(obj.x_axis_select);
                }
                
                $("#y2axis_div").hide();      
                $("#y2axis_lbl").html('Y2 Axis');
                $("#y1axis_lbl").html('Y1 Axis');
                if(chartType=="MSCombiDY2D" || chartType=="MSColumn3DLineDY" || chartType=="StackedColumn3DLineDY" || chartType=="MSStackedColumn2DLineDY" || chartType=="ScrollCombiDY2D"){
                    $("#y2axis_div").show();
                }
                if(chartType=='Bubble'){
                    $("#y2axis_div").show();
                    $("#y2axis_lbl").html('Z Value');
                    $("#y1axis_lbl").html('Y Value');
                }
                
                
                $("#y1_axis_select").dropdownchecklist("destroy");
                $("#y1_axis_select").html(obj.fields);
                $("#y1_axis_select").val(obj.y1_axis_select);
                $("#y1_axis_select").dropdownchecklist({maxDropHeight: 150,width:500,
                    onComplete: function(selector) {
                        redrawChart();
                    }
                });
                $("#y2_axis_select").val(obj.y2_axis_select);
                $("#y2_axis_select").dropdownchecklist({maxDropHeight: 150,
                    onComplete: function(selector) {
                        redrawChart();
                    }
                });
                $("#x_axis_lbl").val(obj.x_axis_lbl);
                $("#y1_axis_lbl").val(obj.y1_axis_lbl);
                $("#y2_axis_lbl").val(obj.y2_axis_lbl);
                
                $("#chart_name").html($("#name_module").val());
                $("#chart_query").html($("#question").val());
                
                $(".graph-div").show();
                unoloaded();
            }
        });
      
    }
</script>
<?php
global $channels;
$moduleId = $_REQUEST['id'];
$moduleQue = "SELECT * FROM module WHERE module_id='" . $moduleId . "'";
$moduleData = select($moduleQue);

$module_name = $moduleData[0]['module_name'];
$internal_module_name = $moduleData[0]['internal_module_name'];
$channel = $moduleData[0]['channel'];
$section = $moduleData[0]['section'];
$question = $moduleData[0]['question'];
$data_query = $moduleData[0]['data_query'];
$status = $moduleData[0]['status'];
$brands = $moduleData[0]['brands'];
$chartColors = $moduleData[0]['chartColors'];
$chart_attr = $moduleData[0]['chartAttr'];
//$axis_selector = $moduleData[0]['axis_selector'];
//$axis = json_decode($axis_selector);
$chartColorsArr = array();
$chartColorsArr = explode(", ", $chartColors);
$daterange = $moduleData[0]['daterange'];

$takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
$takeawayData = select($takeQue);
?>
<div class="clear"></div>
<div class="overlay-bg">
    <div class="overlay-content">
        <center> <p style="margin: 0 !important;" id="popup_msg"></p></center>
    </div>
</div>
<div class="tab_container">
    <form name="modulefrm" id="modulefrm">
        <input type="hidden" name="action" id="action" value="updateModuleData">
        <input type="hidden" name="selected_pages" id="selected_pages" value="">
        <input type="hidden" name="selected_competitor" id="selected_competitor" value="">
        <input type="hidden" name="query_output" id="query_output" value="">
        <input type="hidden" value="<?= $moduleId ?>" name="moduleId" id="moduleId">

        <div class="breadcrumb"> <a href="modules.php">modules</a> <?php if ($channel != '' && $module_name != '') { ?> > <a id="page_breadcrumb" href="modules.php?page=<?php echo $channel; ?>"><?php echo $channel; ?></a> > <a id="section_breadcrumb" href="modules.php?page=<?php echo $channel; ?>&section=<?= $section ?>"><?php echo $section; ?></a> > <a class="selected"><?php echo $module_name; ?></a><?php } ?></div>

        <div class="module-names">
            <div class="module-box" style="margin-left: 0;">
                <div class="name-title">name of module: </div>
                <input type="text" placeholder="Name of Module" name="name" id="name_module" value="<?php echo ($module_name != '') ? $module_name : ''; ?>">
            </div>
            <div class="module-box">
                <div class="name-title">internal module name: </div>
                <input type="text" placeholder="Internal Module" name="internalName" value="<?php echo ($internal_module_name != '') ? $internal_module_name : ''; ?>">
            </div>
            <div class="module-box">
                <div class="name-title">channels: </div>
                <div class="styled-select3">
                    <select name="channel" id="channel">
                        <?php
                        foreach ($channels as $chl => $dbName) {
                            $sel = '';
                            if ($chl == $channel)
                                $sel = 'selected';
                            echo "<option value='" . $chl . "' $sel>" . ucfirst($chl) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="module-box">
                <div class="name-title">sections: </div>
                <div class="styled-select3">
                    <select name="objective" id="objective">
                        <option value="engagements" <?php if ($section != '' && $section == 'engagements') echo "selected"; ?>>Engagements</option>
                        <option value="growth" <?php if ($section != '' && $section == 'growth') echo "selected"; ?>>Growth</option>
                        <option value="customer_service" <?php if ($section != '' && $section == 'customer_service') echo "selected"; ?>>Customer Service</option>
                        <option value="benchmark" <?php if ($section != '' && $section == 'benchmark') echo "selected"; ?>>Benchmark</option>
                        <option value="consumer_insights" <?php if ($section != '' && $section == 'consumer_insights') echo "selected"; ?>>Consumer Insights</option>
                        <option value="test_section" <?php if ($section != '' && $section == 'test_section') echo "selected"; ?>>Test</option>
                    </select>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="module-question">
            <div>what question does this module answer?</div>
            <input type="text" name="question" id="question" value="<?php echo ($question != '') ? $question : ''; ?>">
        </div>

        <!--comment :  Graph and Takeaways :::: Start-->
        <div class="graph-div">
            <div class="graph-heading pull-left">
                <span id="chart_name"></span>
                <p id="chart_query"></p>
            </div>
            <!--        <div class="graph-topic"> Topic Analysis<br/>
                        which topic/product engaged best with consumers? </div>-->
                    <!--<div class="graph-icons"> <a href="#"><img src="images/graph-icon1.png" alt=""></a> <a href="#"><img src="images/graph-icon2.png" alt=""></a> <a href="#"><img src="images/graph-icon3.png" alt=""></a> </div>-->
            <center>
                <div class="graph-img" id="myChart" style="padding-top: 10px;"> 
                </div>
            </center>
            <div class="clear"></div>
        </div>
        <div class="comment-slider">
            <div id="container">
                <div id="content">
                    <div id="slider" style="height: 50px!important;">
                        <ul>
                            <?php
                            if ($takeawayData != array()) {
                                foreach ($takeawayData as $value) {
                                    echo '<li>' . $value['output_query'] . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <!--comment :  Graph and Takeaways :::: End-->

        <div class="data-points">data points:</div>
        <div class="rows-columns">
            <div class="row">SQL Query: </div>
            <div style="text-indent: 5px;">
                <textarea class="span8" name="data_point" id="data_point"><?php echo ($data_query != '') ? $data_query : ''; ?></textarea>
            </div>
        </div>

        <div class="takeaways">Takeaways (<label id="tk_cnt"> <?php echo count($takeawayData); ?></label> )
            <div class="plus-icon"><a data-toggle="modal" href="#myModal" onclick="javascript:$('#tk_question').val('');$('#tk_query').val('');$('#tk_question').focus();"><i class="icon-plus icon-large"></i></a> 
                <!-- Modal -->
                <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                        <h4 class="text-center">Add Takeaways</h4>
                    </div>
                    <div class="modal-body">
                        <div class="span6">
                            <label class="span2">Question : </label>
                            <input type="text" name="tk_question" value="" id="tk_question" class="span4">
                        </div>
                        <div class="span6">
                            <label class="span2">Query : </label>
                            <input type="text" name="tk_query" id="tk_query" class="span4" value="">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="addTakeaways();return false;">Submit</button>
                    </div>
                </div>
                <!-- Modal -->
                <div id="editModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <input type="hidden" name="editId" id="editId" value="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                        <h4 class="text-center">Edit Takeaways</h4>
                    </div>
                    <div class="modal-body">
                        <div class="span6">
                            <label class="span2">Question : </label>
                            <input type="text" name="edtk_question" value="" id="edtk_question" class="span4">
                        </div>
                        <div class="span6">
                            <label class="span2">Query : </label>
                            <input type="text" name="edtk_query" id="edtk_query" class="span4" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="editTakeaways();return false;">Submit</button>
                    </div>
                </div>
            </div>
            <div class="clear"></div>

            <ul class="" id="takeaways_sortable">
                <?php
                if ($takeawayData != array()) {
                    foreach ($takeawayData as $value) {
                        echo '<li style="border: 1px solid black;padding-left: 7px;padding-top: 7px; background-color: #FFFFFF; cursor: pointer;margin-bottom: -1px;">
                            <input type="hidden" name="takeaways_sortorder[]" value="' . $value["takeaway_id"] . '" >
                            <span>' . $value['output_query'] . '</span>
                            <div class="takeaways-button">
                                <div class="delete-button"><a href="" data-toggle="modal" onclick="javascript:get_tkaw(\'' . $value["takeaway_id"] . '\');"><img src="images/edit_takeaway.png" alt="Edit" width="24" height="24"></a></div>
                                <div class="delete-button"><a href="#delModal" data-toggle="modal" onclick="javascript:$(\'#delId\').val(\'' . $value["takeaway_id"] . '\');"><img src="images/delete-icon.png" alt="Delete"></a></div>
                                <div class="sorting-button"><a href="#"><img src="images/sorting-icon.png" alt=""></a></div>
                            </div></li>';
                    }
                }
                ?>

            </ul>
            <div id="delModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="delModalLabel" aria-hidden="true">
                <input type="hidden" name="delId" id="delId" value="">
                <div>
                    <button type="button" class="close" data-dismiss="modal" style="padding: 10px!important;" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div class="span6"><label>Are you want to delete Takeaway?</label></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button class="btn btn-primary" onclick="javascript:deleteTakeaways();return false;">Submit</button>
                </div>
            </div>
        </div>

        <!--<div class="clear"></div>-->
        <!--</form>-->
        <div class="sample-module">Sample Module:
            <div class="clear"></div>
            <div class="sample-box">
                <div class="name-title">SELECT BRAND PAGES: </div>
                <div class="clear"></div>
                <div class="styled-select3">
                    <table>
                        <tr>
                            <td class="checklist-dropdown">
                                <select id="select_module" name="select_module" multiple="multiple">
                                    <?php
                                    $db = $channels[$channel];
                                    $que = "SELECT * FROM $db.brands WHERE brandStatus=0";
                                    $brandData = select($que);

                                    foreach ($brandData as $value) {
                                        if (strpos($brands, $value['handle']) !== false) {
                                            echo "<option value='" . $value['handle'] . "' selected='selected'>" . $value['handle'] . "</option>";
                                        } else {
                                            echo "<option value='" . $value['handle'] . "'>" . $value['handle'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="sample-box">
                <div class="name-title">SELECT COMPETITORS: </div>
                <div class="clear"></div>
                <div class="styled-select3">
                    <table>
                        <tr>
                            <td class="checklist-dropdown">
                                <select id="select_competitor" name="select_competitor" multiple="multiple">

                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="sample-date">
                <div class="name-title">SELECT DATE RANGE: </div>
                <input type="text" name="dateTB" id="dateTB" value="<?php if (isset($daterange) && $daterange != '') echo $daterange; ?>">
            </div>
        </div>
        <div style="clear: both;"></div>
        <div class="rows-columns" id="result_query_div" style="display: none;">
            <div>Resulting SQL Statement: </div>
            <div style="text-indent: 5px;">
                <label class="span8" style="border: 1px solid black;width: 95%;" name="result_query" id="result_query">
                </label>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div style="clear: both;"></div>
        <div class="rows-columns">
            <div class="name-title">Chart: </div><br/>
            <select name="chartType" id="chartType">
                <optgroup label="Single Series Charts">
                    <option value="Column3D" >Column3D</option>
                    <option value="Column2D">Column2D</option>
                    <option value="Line">Line</option>
                    <option value="Area2D">Area2D</option>
                    <option value="Bar2D">Bar2D</option>
                    <option value="Pie2D">Pie2D</option>
                    <option value="Pie3D">Pie3D</option>
                    <option value="Doughnut2D">Doughnut2D</option>
                    <option value="Doughnut3D">Doughnut3D</option>
                    <option value="Pareto2D">Pareto2D</option>
                    <option value="Pareto3D">Pareto3D</option>
                </optgroup>
                <optgroup label="Multi-series Charts">
                    <option value="MSColumn2D" selected="selected">MSColumn2D</option>
                    <option value="MSColumn3D">MSColumn3D</option>
                    <option value="MSLine">MSLine</option>
                    <option value="MSBar2D">MSBar2D</option>
                    <option value="MSBar3D">MSBar3D</option>
                    <option value="MSArea">MSArea</option>
                    <option value="Marimekko">Marimekko</option>
                    <option value="ZoomLine">ZoomLine</option>
                </optgroup>
                <optgroup label="Stacked Charts">
                    <option value="StackedColumn3D">StackedColumn3D</option>
                    <option value="StackedColumn2D">StackedColumn2D</option>
                    <option value="StackedBar2D">StackedBar2D</option>
                    <option value="StackedBar3D">StackedBar3D</option>
                    <option value="StackedArea2D">StackedArea2D</option>
                    <option value="MSStackedColumn2D">MSStackedColumn2D</option>
                </optgroup>
                <optgroup label="100% Stacked Charts">
                    <option value="StackedColumn3D_1">100% StackedColumn3D</option>
                    <option value="StackedColumn2D_1">100% StackedColumn2D</option>
                    <option value="StackedBar2D_1">100% StackedBar2D</option>
                    <option value="StackedBar3D_1">100% StackedBar3D</option>
                </optgroup>
                <optgroup label="Combination Charts">
                    <option value="MSCombi3D">MSCombi3D</option>
                    <option value="MSCombi2D">MSCombi2D</option>
                    <option value="MSColumnLine3D">MSColumnLine3D</option>
                    <option value="StackedColumn2DLine">StackedColumn2DLine</option>
                    <option value="StackedColumn3DLine">StackedColumn3DLine</option>
                    <option value="MSCombiDY2D">MSCombiDY2D</option>
                    <option value="MSColumn3DLineDY">MSColumn3DLineDY</option>
                    <option value="StackedColumn3DLineDY">StackedColumn3DLineDY</option>
                    <option value="MSStackedColumn2DLineDY">MSStackedColumn2DLineDY</option>
                </optgroup>
                <optgroup label="XY Plot Charts">
                    <option value="Scatter">Scatter</option>
                    <option value="Bubble">Bubble</option>
                </optgroup>
                <!--<optgroup label="Scroll Charts">
                    <option value="ScrollColumn2D">ScrollColumn2D</option>
                    <option value="ScrollLine2D">ScrollLine2D</option>
                    <option value="ScrollArea2D">ScrollArea2D</option>
                    <option value="ScrollStackedColumn2D">ScrollStackedColumn2D</option>
                    <option value="ScrollCombi2D">ScrollCombi2D</option>
                    <option value="ScrollCombiDY2D">ScrollCombiDY2D</option>
                </optgroup>
                <optgroup label="Others">
                    <option value="SSGrid">SSGrid</option>
                </optgroup>-->
            </select>
<!--            <select name="chartType" id="chartType">
                <optgroup label="Normal Charts">
                    <option value="MSColumn3D" selected="selected">Column - 3D</option>
                    <option value="MSBar2D">Bar Chart</option>
                    <option value="MSLine">Line Chart</option>
                    <option value="MSArea">Area</option>
                    <option value="MSBar3D">Bar-3D</option>
                    <option value="MSColumn2D">Column</option>
                </optgroup>
                                    <option value="MSCombi2D">Combine - 2D</option>
                                    <option value="MSCombi3D">Combine - 3D</option>
                                    <option value="MSCombiDY2D">CombineDY - 2D</option>
                <optgroup label="100% Stacked">
                    <option value="StackedBar2D_1">Bar</option>
                    <option value="StackedBar3D_1">Bar-3D</option>
                    <option value="StackedColumn2D_1">Column</option>
                    <option value="StackedColumn2DLine">100% Column 2D Line</option>
                    <option value="StackedColumn3D_1">Column-3D</option>
                    <option value="StackedArea2D">100% Area</option>
                                        <option value="StackedColumn3DLine">100% Column 3D Line</option>
                                        <option value="StackedColumn3DLineDY">100% Column 3D LineDY</option>
                </optgroup>
                <optgroup label="Stacked">
                    <option value="StackedBar2D_0">Bar</option>
                    <option value="StackedBar3D_0">Bar-3D</option>
                    <option value="StackedColumn2D_0">Column</option>
                    <option value="StackedColumn3D_0">Column-3D</option>
                </optgroup>
                <optgroup label="Pie">
                    <option value="pie2D">pie2D</option>
                    <option value="pie3D">pie3D</option>
                </optgroup>
            </select>-->
        </div>
        <div style="clear: both;"></div>
        <div class="rows-columns">
            <div>Fusion Chart Customization: </div>
            <div style="text-indent: 5px;">
                <textarea class="span8" name="custom_chart_attr" id="custom_chart_attr" rows="4"><?php echo ($chart_attr != '') ? stripslashes($chart_attr) : ''; ?></textarea>
                <!--<input type="text" class="span8" name="data_point" id="data_point" value="<?php // echo ($data_query != '') ? $data_query : '';                                                                   ?>">-->
            </div>
        </div>

        <div class="module-box span12" >
            <div class="span4">
                <div class="name-title">X Axis (Select One) </div><br/>
                <div class="styled-select3">
                    <select name="x_axis_select" id="x_axis_select">

                    </select>
                </div>
            </div>
            <div class="span4">
                <div>
                    <label>Label</label>
                </div>
                <div>
                    <input type="text" name="x_axis_lbl" id="x_axis_lbl">
                </div>
            </div>
        </div>
        <div class="module-box span12" >
            <div class="span4">
                <div class="name-title" id="y1axis_lbl">Y1 Axis</div><br/>
                <div class="checklist-dropdown2">
                    <select name="y1_axis_select[]" id="y1_axis_select" multiple="multiple">

                    </select>
                </div>
            </div>
            <div class="span4">
                <div>
                    <label>Label</label>
                </div>
                <div>
                    <input type="text" name="y1_axis_lbl" id="y1_axis_lbl">
                </div>
            </div>
        </div>
        <div class="module-box span12" id="y2axis_div">
            <div class="span4">
                <div class="name-title" id="y2axis_lbl">Y2 Axis</div><br/>
                <div class="checklist-dropdown2">
                    <select name="y2_axis_select[]" id="y2_axis_select" multiple="multiple">

                    </select>
                </div>
            </div>
            <div class="span4">
                <div>
                    <label>Label</label>
                </div>
                <div>
                    <input type="text" name="y2_axis_lbl" id="y2_axis_lbl">
                </div>
            </div>
        </div>

        <div class="btn-save2"><a href="javascript:updateModuleData();">Save</a></div>
        <div class="clear"></div>
    </form>


    <!--</form>-->
    <?php
    include_once 'footer.php';
    mysql_close($conn);
    ?>
</div>
</div>
</body>
</html>
