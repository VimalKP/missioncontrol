<?php include_once 'header.php'; ?>
<style type="text/css">
    #sorting_filter{
        padding-right: 7px; padding-bottom: 40px;
    }
</style>
<script type="text/javascript">
    
    var oTable;
    $(document).ready(function() {
        var pagename=getParameterByName('pagename');
        $("#manage_pages").addClass("selected"); //Activate first tab
        
        $("#selectedPage").val('facebook');
        $("#page").val("1");
        
        
        oTable=$('#sorting').dataTable( {
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "iDisplayLength": 20
        } );
        
        $('.menu').removeClass('selected');
        
        if(pagename!=''){
            get_manage_page(pagename);
            $('.addBtn').html(pagename);
            $('#'+pagename).addClass('selected');
        }
        else{
            get_manage_page('facebook');
            $('.addBtn').html('facebook');
            $('#facebook').addClass('selected');
        }
        
        
        $("#searchTB").bind('keypress', function(e) {
            if(e.keyCode==13){
                var searchWord=($(this).val());
                var searchHidden=$("#searchHidden").val();
                $("#searchHidden").val(searchWord);
                //                if(searchWord==''){
                //                    alert("Please Enter some word to seach.");
                //                }
                //                else{
                if(searchWord!='' || searchHidden!=''){
                    $("#page").val('1');
                    get_manage_page($("#selectedPage").val(),searchWord);
                }
            }
        });
        
    });  
    //    function get_manage_page(page,search){
    //        search = typeof search !== 'undefined' ? search : "";
    //        oTable.fnClearTable();
    //        $("#selectedPage").val(page);
    //        $('.addBtn').html(page);
    //        $('.menu').removeClass('selected');
    //        $('#'+page).addClass('selected');
    //        $.ajax({
    //            url:'managepage_ops.php',
    //            type: 'post',
    //            data: {
    //                'managepage':page,
    //                'action':'getPage',
    //                'page':$("#page").val(),
    //                'search':search
    //            },
    //            async:false,
    //            beforeSend: function(){
    //                unoload();
    //            },
    //            success:function(data){
    //                $("#sorting_paginate,#sorting_info,#sorting_length,#sorting_filter").hide();
    //                var obj =$.parseJSON(data);
    //                $("#pageBody").html("");
    //                //                var htmls='';
    //                $.each(obj,function(k,v){
    //                    $("#pagination_ul").html(v.pagination);
    //                });
    //                //                $("#pageBody").html(htmls);
    //                $.each($.parseJSON(data), function(id,val) {
    //                    var fanGrowth=parseInt(this.end_fans)-parseInt(this.start_fans);
    //                    var perFan=(fanGrowth/this.start_fans);
    //                    if(id!=''){
    //                        if(page=='instagram'){
    //                            var mainid="<a href='http://www.instagram.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;' target='_blank'>"+id+"<a>";
    //                        }else if(page=='facebook'){
    //                            var mainid="<a href='http://www.facebook.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
    //                            //                            $('#channel_iframe').html("<a href='http://www.facebook.com/"+obj.handle+"' target='_blank'>http://www.facebook.com/"+obj.handle+" <a>"+lable+" ")
    //                        
    //                        }else if(page=='twitter'){
    //                            var mainid="<a href='http://www.twitter.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
    //                            //                            $('#channel_iframe').html("<a href='http://www.twitter.com/"+obj.handle+"' target='_blank'>http://www.twitter.com/"+obj.handle+" <a>"+lable+" ")
    //                             
    //                        }else{
    //                            var mainid="<a href='http://www.youtube.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
    //                            //                            $('#channel_iframe').html("<a href='http://www.youtube.com/"+obj.handle+"' target='_blank'>http://www.youtube.com/"+obj.handle+" <a>"+lable+" ")
    //                        }
    //                        var a = oTable.fnAddData([
    //                            mainid,
    //                            (typeof this.first_date=="undefined")?'-':this.first_date,
    //                            (typeof this.last_date=="undefined")?'-':this.last_date,
    //                            (typeof this.start_fans=="undefined")?'-':this.start_fans,
    //                            (typeof this.end_fans=="undefined")?'-':this.end_fans,
    //                            (isNaN(fanGrowth))?'0':fanGrowth,
    //                            (isNaN(perFan))?'0':perFan.toFixed(3),
    //                            (typeof this.engagements=="undefined")?'-':this.engagements,
    //                            (typeof this.users=="undefined")?'-':this.users,
    //                            '<a data-toggle="modal" href="#delModal" onclick="javascript:$(\'#delId\').val(\''+id+'\');"><img id='+id+' src="images/icon-delete.png"></a>']
    //                    );
    //                    }
    //                    else{
    //                        $("#pageBody").html("<tr><td colspan='10'>No data available in table.</td></tr>");
    //                    }
    //                });
    //                
    //                $("#sorting_paginate,#sorting_info,#sorting_length,#sorting_filter").hide();
    //                unoloaded();
    //            }
    //        });
    //    }
    function addBrand(){
        $('.control-group').removeClass('error');
        var selectedPage=$("#selectedPage").val();
        var handle=$("#handle").val();
        
        if(handle==''){
            $('.control-group').addClass('error');
            $("#handle").focus();
        }
        else{
            
            $.ajax({
                url:'managepage_ops.php',
                type: 'post',
                data: {
                    'handle':handle,
                    'selectedPage':selectedPage,
                    'action':'addBrand'
                },
                beforeSend: function(){
                    //                    unoload();
                },
                success:function(data){
                    $('#myModal').modal('hide')
                    //                    var start=$("#start").val();
                    //                    var limit=$("#limit").val();
                    get_manage_page(selectedPage);
                    //                    $("#myModal").hide();
                    //                    $(".modal-backdrop").hide();
                    //                    console.log(data==true);
                    unoloaded();
                }
            });
        }
    }
    function deleteHandle(){
        var handle=$("#delId").val();
        var selectedPage=$("#selectedPage").val();
        if (handle!='')
        {
            $.ajax({
                url:'managepage_ops.php',
                type: 'post',
                data: {
                    'handle':handle,
                    'selectedPage':selectedPage,
                    'action':'deleteBrand'
                },
                beforeSend: function(){
                    //                    unoload();
                },
                success:function(data){
                    $('#delModal').modal('hide')
                    get_manage_page(selectedPage,$("#searchTB").val());
                    unoloaded();
                }
            });
        }
        else
        {
            alert("handle not found");
        }
    }
    function goto_page(page){
        $("#page").val(page);
        var page=$("#selectedPage").val();
        get_manage_page(page,$("#searchTB").val());
    }
</script>
<?php
global $channels;
?>
<div class="tab_container">
    <input type="hidden" value="" name="selectedPage" id="selectedPage">
<!--    <input type="hidden" value="" name="start" id="start">
    <input type="hidden" value="10" name="limit" id="limit">-->
    <input type="hidden" value="0" name="page" id="page">
    <input type="hidden" value="" name="searchHidden" id="searchHidden">
    <div id="tab2" class="tab_content" style="padding: 1px;">
        <div class="tab-nav">
            <ul class="tab-nav">
                <?php
                foreach ($channels as $ch => $db) {
                    echo "<li><a onclick='javascript:$(\"#page\").val(\"1\");$(\"#searchTB\").val(\"\"); get_manage_page(this.id); ' class='menu' id='$ch' href='javascript:void(0);'>" . ucfirst($ch) . "</a></li>";
                }
                ?>
            </ul>
            <div class="clear"></div>
        </div>
        <div style="float: right; padding: 14px 11px 0px 0px;">
            <label>Search: <input id="searchTB" type="text" aria-controls="sorting"></label>
        </div>
        <div class="clear"></div>
        <div id="facebook" class="tab_content" style="margin-top: 10px;">
            <table class="table table-striped table-bordered" id="sorting">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Tracked Since</th>
                        <th>Last Updated</th>
                        <th>Start Fans</th>
                        <th>End Fans</th>
                        <th>Fan Growth</th>
                        <th>Fan % Change</th>
                        <th>Engagements</th>
                        <th>Engaged Users</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="pageBody">
                </tbody>
            </table>
            <div id="delModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="delModalLabel" aria-hidden="true">
                <input type="hidden" name="delId" id="delId" value="">
                <div>
                    <button type="button" class="close" data-dismiss="modal" style="padding: 10px!important;" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div class="span6"><label>Are you want to delete this Brand?</label></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    <button class="btn btn-primary" onclick="javascript:deleteHandle();">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="btn-facebook-pages" style="width: 200px;">
            <a data-toggle="modal" href="#myModal">add <span class="addBtn"></span> pages</a> 
            <!-- Modal -->
            <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h4 class="text-center"><span class="addBtn"></span></h4>
                </div>
                <div class="modal-body">
                    <!--<span class="error" style="display: none;" id="brandErr"></span>-->
                    <div class="span1" style="line-height: 88px;"><label>brand : &nbsp;&nbsp;</label> </div>
                    <div class="control-group">
                        <div class="controls">
                            <label class="span3" style="float: right; font-size: 11px;">e.g. : name1,name2,name3</label>
                            <input type="text" id="handle"  class="span5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="javascript:addBrand();">Submit</button>
                </div>
            </div>
        </div>
        <div class="pagination pagination-right">
            <ul id="pagination_ul">
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <?php include_once 'footer.php'; ?>
</body>
</html>
