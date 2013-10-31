<?php include_once 'header.php'; ?>
<style type="text/css">
    table { table-layout: fixed; }
 table th, table td { overflow: hidden; }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        var qa_contact_id='<?= $_SESSION['qa_contact_id'] ?>';
        $("#errorlog").addClass("selected"); //Activate first tab
        openErrorLog("facebook");
        //        $("#facebook").css("text-decoration","none").css("color","#000000");
    });
    
    function openErrorLog(page){
        $(".brand").css("text-decoration","underline").css("color","#085394");
        $("#"+page).css("text-decoration","none").css("color","#000000");
        $.ajax({
            url:'error_log_ops.php',
            type: 'post',
            data: {
                'brand':page,
                'action':'fetcherrorpage'
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                var obj =$.parseJSON(data);
                var html='<tr role="row"><th style="width: 75px;">Date</th><th style="width: 80px;">Error Code</th><th>Error Message</th><th>Output</th><th>API Call</th></tr>';
                for(i=0;i<obj.length;i++){
                    html+='<tr role="row">';
                    html+='<td>'+obj[i]['error_date']+'</td>';
                    html+='<td>'+obj[i]['error_code']+'</td>';
                    html+='<td>'+obj[i]['error_message']+'</td>';
                    html+='<td>'+obj[i]['output']+'</td>';
                    html+='<td class="column">'+obj[i]['api_call']+'</td>';
                    html+="</tr>";
                }
                $("#errorTablemain").html('');
                $("#errorTablemain").html(html);
                unoloaded();
            }
        });
    }
    
    
</script>
<div class="tab_container">
    <div id="tab3" class="tab_content" style="padding: 1px;">
        <div class="tab-nav">
            <ul class="tab-nav">
                <li><a class="facebook brand" id="facebook" href="javascript:openErrorLog('facebook');">Facebook</a></li>
                <li><a class="twitter brand"  id="twitter" href="javascript:openErrorLog('twitter');">Twitter</a></li>
                <li><a class="youtube brand" id="youtube" href="javascript:openErrorLog('youtube');">Youtube</a></li>
                <li><a class="instagram brand" id="instagram" href="javascript:openErrorLog('instagram');" style="border:none;" >Instagram</a></li>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="qa-content">
            <div id="data">
            </div>
            <!--            <div id="data_qapage" >
                            <table id="errorTable" class="table table-striped table-bordered dataTable" aria-describedby="sorting_info">
                                <tr role="row">
                                    <th>Error Id</th>
                                    <th>Channel</th>
                                    <th>API Call</th>
                                    <th>Output Error</th>
                                </tr>
                            </table>
                        </div>-->
            <div id="data_qapage2" style="height: 500px;overflow-y: scroll;">
                <table id="errorTablemain" class="table table-striped table-bordered dataTable" aria-describedby="sorting_info">
<!--                    <tr role="row">
                        <th>Error Id</th>
                        <th>Channel</th>
                        <th>API Call</th>
                        <th>Output Error</th>
                    </tr>-->
                </table>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <?php include_once 'footer.php'; ?>
</div>
</div>
</body>
</html>
