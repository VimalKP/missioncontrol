<?php
include_once 'header.php';
global $channels;
?>
<style type="text/css">
    td:nth-child(3){text-transform: capitalize;}
</style>
<script type="text/javascript">
    $(document).ready(function() {
        
        $("#channel").val('<?= $_GET['page']; ?>');
        $("#searchChannel").val('<?= $_GET['page']; ?>');
        $("#objective").val('<?= $_GET['section']; ?>');
        $("#searchObj").val('<?= $_GET['section']; ?>');
        //Default Action
        //        $(".tab_content").hide(); //Hide all content
        $("#module_page").addClass("selected"); //Activate first tab
        //        addNewModule();
        
        $("#add_modules_page").hide();
        //            $("#slider").easySlider();
        //        $("#s1").dropdownchecklist();
        
        oTable=$('#sorting').dataTable( {
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "iDisplayLength": 20
        } );
        $("#searchHidden").val();
        $("#searchChannel").val();
        $("#searchObj").val();
        $("#searchTB").bind('keypress', function(e) {
            if(e.keyCode==13){
                var searchWord=($(this).val());
                var searchHidden=$("#searchHidden").val();
                $("#searchHidden").val(searchWord);
                
                if(searchWord!='' || searchHidden!=''){
                    $("#page").val('1');
                    get_module_page();
                }
            }
        });
        $("#channel").live('change',function(){
            var searchChnl=($(this).val());
            
            $("#searchChannel").val(searchChnl);
            var searchHidden=$("#searchChannel").val();    
            if( searchHidden!=''){
                $("#page").val('1');
                get_module_page();
            }
        });
        $("#objective").live('change',function(){
            var searchObj=($(this).val());
            $("#searchObj").val(searchObj);
            var searchHidden=$("#searchObj").val();

            if( searchHidden!=''){
                $("#page").val('1');
                get_module_page();
            }
        });
        get_module_page();
    }); 
    
    function addNewModule(){
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'addmodule'
            },
            success:function(data){
                window.location='edit_modules.php?id='+data;
            }
        });  
        
    }
    function deleteModule(){
        var moduleId=$("#delId").val();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'deletemodule',
                'moduleId':moduleId
            },
            success:function(data){
                window.location='modules.php';
            }
        });
    }
    
    function get_module_page(){
        //        search = typeof search !== 'undefined' ? search : "";

        var searchWord=($("#searchHidden").val());
        var channel=($("#searchChannel").val());
        var objective=($("#searchObj").val());

        var srhString='';
        if((searchWord!=''))
            srhString+=' AND module_name LIKE "%'+searchWord+'%"';
        if(channel!='' && ($("#channel").val()!="0"))
            srhString+=' AND channel="'+channel+'"';
        if(objective!='' && ($("#objective").val()!="0"))
            srhString+=' AND section="'+objective+'"';

        oTable.fnClearTable();
        $.ajax({
            url:'module_ops.php',
            type: 'post',
            data: {
                'action':'getPage',
                'page':$("#page").val(),
                'search':srhString
            },
            async:false,
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                
                $("#sorting_paginate,#sorting_info,#sorting_length,#sorting_filter").hide();
                var obj =$.parseJSON(data);
                $("#content").html(obj.content);
                $("#pageBody").html("");
                //                var htmls='';
                $.each(obj,function(k,v){
                    $("#pagination_ul").html(v.pagination);
                });
                //                $("#pageBody").html(htmls);
                $.each($.parseJSON(data), function(id,val) {
                    if(id!=''){
                        var a = oTable.fnAddData([
                            (typeof this.module_name=="undefined")?'-':'<a class="module_title_a" href="edit_modules.php?id='+id+'">'+this.module_name+'</a>',
                            (typeof this.channel=="undefined")?'-':this.channel,
                            (typeof this.section=="undefined")?'-':(this.section).replace(/[_\s]/g, ' '),
                            (typeof this.question=="undefined")?'-':this.question,
                            (typeof this.takeaway=="undefined")?'-':this.takeaway,
                            '<a href="edit_modules.php?id='+id+'"><img src="images/icon-edit.png"></a><a data-toggle="modal" href="#delModal" onclick="javascript:$(\'#delId\').val(\'' + id + '\');"><img src="images/icon-delete.png"></a>'
                        ]
                    );
                    }
                    else{
                        $("#pageBody").html("<tr><td colspan='10'>No data available in table.</td></tr>");
                    }
                });
                                
                $("#sorting_paginate,#sorting_info,#sorting_length,#sorting_filter").hide();
                unoloaded();
            }
        });
    }
    
    function goto_page(page){
        $("#page").val(page);
        get_module_page();
    }
</script>
<div class="tab_container" id="modules_page">
    <input type="hidden" value="0" name="page" id="page">
    <input type="hidden" value="" name="searchHidden" id="searchHidden">
    <input type="hidden" value="" name="searchChannel" id="searchChannel">
    <input type="hidden" value="" name="searchObj" id="searchObj">
    <!--    <div id="add_modules_page">
    <?php
//    $moduleQue = "SELECT * FROM module WHERE status='1'";
//    $moduleData = select($moduleQue);
    ?>
        </div>-->
    <div id="tab4" class="tab_content" style="padding: 5px;">
        <div class="module-top">
            <div class="module-search-box">
                <input type="search" placeholder="search" name="searchTB" id="searchTB">
            </div>
            <div class="drop-down">
                <div class="styled-select" id="selected_channel">
                    <select name="channel" id="channel">
                        <option value="0" selected="selected">Select Channel</option>
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
                <div class="styled-select" id="section">
                    <select name="objective" id="objective">
                        <option value="0" selected="selected">Select Section</option>
                        <option value="engagements" >Engagements</option>
                        <option value="growth" >Growth</option>
                        <option value="customer_service" >Customer Service</option>
                        <option value="benchmark" >Benchmark</option>
                        <option value="consumer_insights" >Consumer Insights</option>
                        <option value="test_section" >Test</option>
                    </select>
                </div>
            </div>
            <a href="javascript:void(0)" onclick="return addNewModule();" class="plus-icon" id="addnewmodule"><i class="icon-plus icon-large"></i></a>
            <div class="clear"></div>
        </div>
        <div id="delModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="delModalLabel" aria-hidden="true">
            <input type="hidden" name="delId" id="delId" value="">
            <div>
                <button type="button" class="close" data-dismiss="modal" style="padding: 10px!important;" aria-hidden="true">x</button>
            </div>
            <div class="modal-body">
                <div class="span6"><label>Are you want to delete this Module?</label></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
                <button class="btn btn-primary" onclick="javascript:deleteModule();">Submit</button>
            </div>
        </div>
        <table class="table table-striped table-bordered" id="sorting">
            <thead>
                <tr>
                    <th>Module name</th>
                    <th>Channel</th>
                    <th>Section</th>
                    <th>Question</th>
                    <th>Takeaways</th>
                    <th>Edit/Delete</th>
                </tr>
            </thead>
            <tbody id="content">
                <?php
//                if ($moduleData != array() && count($moduleData) > 0) {
//                    foreach ($moduleData as $value) {
//                        $moduleId = $value['module_id'];
//                        $takeQue = "SELECT * FROM takeaways WHERE module_id_fk='" . $moduleId . "' AND status='1' ORDER BY priority ASC";
//                        $takeawayData = select($takeQue);
//
//                        echo '<tr><td>' . $value['module_name'] . '</td>';
//                        echo '<td>' . $value['channel'] . '</td>';
//                        echo '<td>' . $value['section'] . '</td>';
//                        echo '<td>' . $value['question'] . '</td>';
//                        echo '<td>' . count($takeawayData) . '</td>';
//                        echo '<td>
//                                    <a href="edit_modules.php?id=' . $moduleId . '"><img src="images/icon-edit.png"></a>
//                                    <a data-toggle="modal" href="#delModal" onclick="javascript:$(\'#delId\').val(\'' . $moduleId . '\');"><img src="images/icon-delete.png"></a>
//                                </td></tr>';
//                    }
//                } else {
//                    echo '<tr><td colspan="6"> Module Data not available! </td></tr>';
//                }
                ?>
            </tbody>
        </table>
        <div class="pagination pagination-right">
            <ul id="pagination_ul">
            </ul>
        </div>
    </div>
    <?php include_once 'footer.php'; ?>
</div>
</div>
</body>
</html>
