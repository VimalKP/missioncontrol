<?php include_once 'header.php'; ?>
<!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />-->
<!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
<!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
<script type="text/javascript">
    var ques;
    var answers;
    var ques_size;
    var all_ques_size;
    var main_que_arr=new Array();
    var main_qaid_arr=new Array();
    var postID=new Array();
    var userpostId=new Array();
    var engpostId=new Array();
    $(document).ready(function() {
        var qa_contact_id='<?= $_SESSION['qa_contact_id'] ?>';
        $("#qa_page").addClass("selected"); //Activate first tab
        //        fillEmailTo(qa_contact_id);
        fetchqaresult(qa_contact_id);
        $('#extra_id').val('');
        $('#inputed_answer').bind('keypress', function(e) {
            if(e.keyCode==13){
                getNextQue("next");
            }
        });
        //        $( "#brand_list" ).combobox();
    });
    //    })( jQuery );
    

    //});    
  
    function fetchqaresult(qa_contact_id){
        $.ajax({
            //            dataType: "json",
            //            contentType: "application/json",
            url: "qa_ops.php",
            type:"POST",
            async:false,
            data:{
                "contactId":qa_contact_id,
                "action":"fetchqaresult_history"
            },
            success: function(data){
                console.log(data)
                var obj =$.parseJSON(data);
                var tbodymain2='';
                //                console.log(obj.datatext);
                var maintable='<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example"><thead><tr><th>Network</th><th>Last Checked</th><th>Verified By</th><th>Score</th><th>Status</th></tr> </thead><tbody>';
                if(obj[0].length>0){
                    for(i=0;i<obj[0].length;i++){
                        if(i % 2 == 0){
                            var trclass="odd";
                        }else{
                            var trclass="even";
                        }
                        if(obj[0][i].status=='Pass'){
                            var status="images/pass.png"
                            var status_class= "green";
                        }else if(obj[0][i].status=='Fail'){
                            var status="images/fail.png"
                            var status_class= "red";
                            
                        }else{
                            var status="images/varification.png"
                            var status_class= "dark-yellow";
                        }
                        var str=obj[0][i].channel;
                        var brandid=str.toLowerCase();
                        var z_testid_pk=obj[0][i].z_testid_pk
                        //                    if(brandid!='facebook'){
                        var  tbodymid='<tr class="'+trclass+'"><td style="text-align: left;"><span id="'+brandid+'" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="'+obj[0][i].img+'" alt="" />'+obj[0][i].channel+'</span></td><td>'+obj[0][i].checked_date+'</td><td>'+obj[0][i].username+'</td><td class="'+status_class+' large-font">'+obj[0][i].score+'/'+obj[0][i].testsize+'</td><td class="green"><span style="cursor:pointer;" onclick="fetchresultpage('+z_testid_pk+')"><img src="'+status+'" alt="" /></span></td></tr>';
                        tbodymain2+=tbodymid;
                        //                    }
                    
                   
                    }
                }
                $("#mailTo").val(obj[1].email);
                var tbodyend='</tbody></table>';
                var  finaltable=maintable+tbodymain2+tbodyend;
                $("#data").append(finaltable);
                if($("#data").find("#facebook").length<=0){
                    var fbdata='<tr class="qa-even"><td style="text-align: left;"><span id="facebook" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="images/facebook.png" alt="" />Facebook</span></td><td>-</td><td>-</td><td class="green large-font">-/-</td><td class="green"><img src="" alt="" /> Not Yet Tested </td></tr>';
             
                    $("#tablebody").append(fbdata);
                }else if($("#data").find("#twitter").length<=0){
                    var ttdata='<tr class="qa-even"><td style="text-align: left;"><span id="twitter" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="images/twitter.png" alt="" />Twitter</span></td><td>-</td><td>-</td><td class="green large-font">-/-</td><td class="green"><img src="" alt="" /> Not Yet Tested </td></tr>';
             
                    $("#tablebody").append(ttdata);
                }else if($("#data").find("#youtube").length<=0){
                    var ytdata='<tr class="qa-odd"><td style="text-align: left;"><span id="youtube" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="images/youtube.png" alt="" />Youtube</span></td><td>-</td><td>-</td><td class="green large-font">-/-</td><td class="green"><img src="" alt="" /> Not Yet Tested </td></tr>';
             
                    $("#tablebody").append(ytdata); 
                }else if($("#data").find("#instagram").length<=0){
                    var itdata='<tr class="qa-odd"><td style="text-align: left;"><span id="instagram" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="images/instagram.png" alt="" />Instagram</span></td><td>-</td><td>-</td><td class="green large-font">-/-</td><td class="green"><img src="" alt="" /> Not Yet Tested </td></tr>';
             
                    $("#tablebody").append(itdata);
                }
            },
            error: function(data, status){
                console.log(status + ": " +data.status + " " + data.statusText);
            }
        });
    }
    function fetchqapage(brand){
        //        alert(brand);
        
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'brand':brand,
                'action':'fetchqapage'
            },
            beforeSend: function(){
                //                unoload();
            },
            success:function(data){
                //                console.log(data);
                var obj =$.parseJSON(data);
                //                console.log(obj['branddetail'].list)
                //                $("#mailTo").val(emails)
                //                alert(data);
                $('#data_qapage').css("display","block"); 
                $('#data').css("display","none"); 
                $('#email_page').css("display","none");
                $('#brand_list').html('');
                $('#brand_list').append(obj['branddetail'].list);
                $('.dropdown-menu').html('');
                $( "#brand_list" ).combobox().data('combobox').refresh();
                $( "#brand_list" ).combobox();
                $(".brand").css("text-decoration","underline").css("color","#085394");
                $("."+brand).css("text-decoration","none").css("color","black");
                $("#brand_image").attr("src","");
                $("#brand_name").html("");
                $("#brand_image").attr("src",obj['branddetail'].img);
                $("#brand_name").append(obj['branddetail'].channel);
                $("#lastchechked").html("");
                $("#previous_status").html("");
                $("#total_checks").html("");
                $("#lastchechked").append(obj['branddetail'].lastchechked);
                $("#previous_status").append(obj['branddetail'].previous_status);
                $("#total_checks").append(obj['branddetail'].total_checks);
                if(obj['branddetail'].previous_status=='Pass'){
                    var status_icon="images/pass.png" 
                    //                    $("#previous_status").css("color","#008000");
                }else if(obj['branddetail'].previous_status=='Fail'){
                    var status_icon="images/fail.png" 
                    //                    $("#previous_status").css("color","#cc0000");
                }else{
                    var status_icon="images/varification.png" 
                }
                $("#previous_status_icon").attr("src",status_icon);
                $("#review_data").attr("href","javascript:fetchreviewdata('"+brand+"')");
                $("#history_data").attr("href","javascript:fetchhistorydata('"+brand+"')");
                //                $("#history_data").addClass("selected");
                $("#channel_name").val(brand);
                $("#brand_list").removeAttr("disabled");
                $("#brand_search_box").removeAttr("disabled");
                //                 $("#brand_search_box").attr("disabled","disabled");
                //                    $("#brand_list").attr("disabled","disabled");
                $(".dropdown-toggle").bind("click");
                $("#display_result_page").css("display",'none');
                //                console.log($(".dropdown-toggle").attr("onclick"));
                fetch_individualbrandqa_page()
                
                //                unoloaded();
                  
            }
        });

    }
    function fetchreviewdata(brand){
     
        window.location.href="qa.php";
    }
    function fetchhistorydata(brand){
        if(!$('#history_data').hasClass('selected')){
            $("#history_data").addClass("selected");
            $("#review_data").removeClass("selected");
            $.ajax({
                url:'qa_ops.php',
                type: 'post',
                data: {
                    'brand':brand,
                    'action':'fetchhistorydata'
                },
                beforeSend: function(){
                    unoload();
                },
                success:function(data){
                    console.log(data);
                    var obj =$.parseJSON(data);
                    $("#history_review_data").css("display","block");
                    $("#history_review_data").html('');
                    $("#history_review_data").html(obj['brandhistorydetail'].list);
                    $("#main_frame").css("display","none");
                    $("#display_result_page").css("display",'none');
                    unoloaded();
                }
            });
        }
        
    }
    function fetch_individualbrandqa_page(){
      
        $('#z_testid_pk').val('');
        var brandname=$("#brand_list").val();
        var channel_name=$("#channel_name").val();
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'brandname':brandname,
                'channel_name':channel_name,
                'action':'fetch_individualbrandqa_page'
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                console.log(data);
                var obj =$.parseJSON(data);
                $('#z_testid_pk').val('');
                $("#history_data").removeClass("selected");
                $("#review_data").removeClass("selected");
                
                $("#main_frame").css("display","block");
                $("#history_review_data").css("display","none");
                //                Random Selected User :obj.randomeusername
                $(".random-name").text('');
                $(".random-name").text(' Random Selected User :'+obj.randomeusername);
                //                Random Selected User :obj.userhandle
                
                if(obj.randomhandle!=''){
                    $("#brand_list").val(obj.randomhandle);
                    $("#brand_search_box").val(obj.randomhandle);
                    
                    //                    set_channel_iframe(obj.handle,obj.channel);
                }else if(obj.userhandle!=''){
                    $("#userhandle").val(obj.userhandle);
                    $("#extra_id").val(obj.userID);
                    set_channel_iframe(obj.userhandle,obj.channel);
                }
                //                else{
                //                    set_channel_iframe(obj.handle,obj.channel);
                //                }
               
                postID=obj.postIdarray;
                userpostId=obj.userpostIdarray;
                engpostId=obj.engagementIdarray;
                ques=(obj.question);
                answers=(obj.answers);
                ques_size=(obj.quesize);
                all_ques_size=(obj.allquesize);
                set_question_link(ques[0]['questionType'],ques[0]['url']);
                $("#qa_index").val(0);
                $("#main_index").val(1);
                //                if(ques[0]['questionType']=='posts'){
                //                    var link=postID[0]['link'];
                //                    $('#channel_iframe').html("");
                //                    $('#post_link').val(''); 
                //                    var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
                //                    $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
                //                    $('#post_link').val(link); 
                //                }
                $("#trackNo").html("1/"+obj.quesize+" :");
                $("#question").html(ques[0]['question']);
                $("#qa_id").val(ques[0]['qa_id']);
                
                $(".qa-paginate").html(obj.pagination);
                $("#prevPage").attr('onclick','getPageData(1)');
                $("#nextPage").attr('onclick','getPageData(2)');

                $(".pages").removeClass('selectedPage');
                $("#page_1").addClass('selectedPage');
                main_que_arr=[];
                main_qaid_arr=[];
                for(var i=0;i<ques.length;i++){
                    //                    console.log(i + " -- "+ques[i]['question']);
                    if(ques[i]['parent_qa_id']==0){ //means it is main que
                        main_que_arr.push(i);
                        main_qaid_arr.push(ques[i]['qa_id']);
                    }
                }
                $("#display_result_page").css("display",'none');
                $("#top_bar").css("display",'block');
                $("#brand_list").removeAttr("disabled");
                $("#brand_search_box").removeAttr("disabled");
                //                 $("#brand_search_box").attr("disabled","disabled");
                //                    $("#brand_list").attr("disabled","disabled");
                $(".dropdown-toggle").bind("click");
                unoloaded();
            }
        });
    }
    function set_channel_iframe(handle,channel){
        $('#channel_iframe').html("");
        var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
        if(channel=='instagram'){
            $('#channel_iframe').html("<a href='http://web.stagram.com/n/"+handle+"' target='_blank'>http://web.stagram.com/n/"+handle+"<a>"+lable+" ")
        }else if(channel=='facebook'){
            $('#channel_iframe').html("<a href='http://www.facebook.com/"+handle+"' target='_blank'>http://www.facebook.com/"+handle+" <a>"+lable+" ")
                        
        }else if(channel=='twitter'){
            $('#channel_iframe').html("<a href='http://www.twitter.com/"+handle+"' target='_blank'>http://www.twitter.com/"+handle+" <a>"+lable+" ")
                             
        }else{
            $('#channel_iframe').html("<a href='http://www.youtube.com/"+handle+"' target='_blank'>http://www.youtube.com/"+handle+" <a>"+lable+" ")
        }
                
    }
    function set_question_link(table){
        var channel_name=$("#channel_name").val();
        var brand=$('#brand_list').val();
        if(brand=='' || brand==null){
            brand=$("#channel_brand_name").val();
        }
        var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
        if(table=='users'){
            var userhandle=$("#userhandle").val();
         
            set_channel_iframe(userhandle,channel_name);
        }else if(table=='posts'){
            //           if(ques[0]['questionType']=='posts'){
            var link=postID[0]['link'];
            console.log(postID);
            $("#extra_id").val('');
            $("#extra_id").val(postID[0]['postID']);
            $('#channel_iframe').html("");
            $('#post_link').val(''); 
            $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
            $('#post_link').val(link); 
            //                }
        }else if(table=='userposts'){
            var link;
            //           if(ques[0]['questionType']=='posts'){
            //            https://www.facebook.com/40796308305/posts/10152148484323306
            if(channel_name=='facebook'){
                var postarr= userpostId[0]['postID'].split("_");
                link="https://www.facebook.com/"+ postarr[0]+"/posts/"+postarr[1];
                $("#extra_id").val('');
                $("#extra_id").val(userpostId[0]['postID']);
                $('#channel_iframe').html("");
                $('#post_link').val(''); 
                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
                $('#post_link').val(link); 
                //               postarr[0] postarr[1]
            }else if(channel_name=='twitter'){
                link=  "https://twitter.com/"+userpostId[0]['handle']+"/status/"+userpostId[0]['postID'];
                $("#extra_id").val('');
                $("#extra_id").val(userpostId[0]['postID']);
                $('#channel_iframe').html("");
                $('#post_link').val(''); 
                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
                $('#post_link').val(link); 
            }else if(channel_name=='youtube'){
                if(userpostId[0]['postID']==''){
                    set_channel_iframe(brand,"youtube");
                }else{
                    ("#extra_id").val('');
                    $("#extra_id").val(userpostId[0]['postID']);
                    $('#channel_iframe').html("");
                    $('#post_link').val(''); 
                    $('#channel_iframe').html("<a href='"+userpostId[0]['link']+"' target='_blank'>"+userpostId[0]['link']+"<a>"+lable+" ")
                    $('#post_link').val(userpostId[0]['link']); 
                }
            }
        }
        else if(table=='engagements'){
            if(channel_name=='facebook' || channel_name=='instagram' || channel_name=='youtube'){
                var link=postID[0]['link'];
                console.log(postID);
                $("#extra_id").val('');
                $("#extra_id").val(postID[0]['postID']);
                $('#channel_iframe').html("");
                $('#post_link').val(''); 
                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
                $('#post_link').val(link); 
            }else{
                var link=  "https://twitter.com/"+engpostId[0]['handle']+"/status/"+engpostId[0]['engagementID'];
                console.log(engpostId)
                $("#extra_id").val('');
                $("#extra_id").val(engpostId[0]['engagementID']);
                $('#channel_iframe').html("");
                $('#post_link').val(''); 
                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
                $('#post_link').val(link); 
            }
        }else{
            $("#extra_id").val('');
            set_channel_iframe(brand,channel_name);
        }
    }
    function getNextQue(type){
        var qa_index=$("#qa_index").val();
        var main_index=$("#main_index").val();
        var z_testid_pk=$('#z_testid_pk').val();
        
        //        update_score();
        if(type=='next'){
            if(qa_index>=parseInt(all_ques_size)-1){
                find_answeres();
                update_score();
                alert('Thnx for answering');
                fetchresultpage(z_testid_pk);
                //                fetchqaresult(qa_contact_id)/
                //                send_mail();
                return false;
            }
            else{
                find_answeres();
                var extra_id=$("#extra_id").val();
                if(parseInt(ques[qa_index]['sampleSize'])>1 && extra_id==''){
                    console.log('switch question')
                    if((ques[qa_index]['parent_qa_id']==0)){
                        main_index=parseInt(main_index)+1;
                        $("#main_index").val(main_index);
                    }
                }else{
                    console.log('main_index-->'+main_index);
                    qa_index=parseInt(qa_index)+1;
                    $("#qa_index").val(qa_index);
        
                    if((ques[qa_index]['parent_qa_id']==0)){
                        main_index=parseInt(main_index)+1;
                        $("#main_index").val(main_index);
                    }
                    $("#trackNo").html((main_index)+"/"+ques_size+" :");
                    $("#question").html(ques[qa_index]['question']);
                
                    $('#qa_id').val(ques[qa_index]['qa_id']);
                    var field=ques[qa_index]['field'];
                    console.log(field);
                    //                    $('#qa_id').val();
                    if(field == 'postType' || field == 'tweetType ' || field == 'engagementType' || field == 'sex'){
                        fetch_post_dropdown(ques[qa_index]['qa_id']);
                    }else{
                        $("#inputed_answer").show();
                        $("#dropdown_post").hide();
                    }
                }
        
            }
        }
        else{
            if(qa_index==0){
                alert('Over');
                return false;
            }
            else{
                //                var extra_id=$('#extra_id').val();
                
                if((ques[qa_index]['parent_qa_id']==0)){
                    main_index=parseInt(main_index)-1;
                    $("#main_index").val(main_index);
                }
                qa_index=parseInt(qa_index)-1;
                $("#qa_index").val(qa_index);
        
                $("#trackNo").html((main_index)+"/"+ques_size+" :");
                $("#question").html(ques[qa_index]['question']);
                $('#qa_id').val(ques[qa_index]['qa_id']);
                var field=ques[qa_index]['field'];
                console.log(field);
                //                    $('#qa_id').val();
                if(field == 'postType' || field == 'tweetType ' || field == 'engagementType' || field == 'sex'){
                    fetch_post_dropdown(ques[qa_index]['qa_id']);
                }else{
                    $("#inputed_answer").show();
                    $("#dropdown_post").hide();
                }
            }
        }
        if(main_index>1){
            $("#prevPage").attr('onclick','getPageData('+(parseInt(main_index)-1)+')');
        }
        else{
            $("#prevPage").attr('onclick','getPageData(1)');
        }
        if(main_index<ques_size){
            $("#nextPage").attr('onclick','getPageData('+(parseInt(main_index)+1)+')');
        }
        else{
            $("#nextPage").attr('onclick','getPageData('+ques_size+')');
        }

        $(".pages").removeClass('selectedPage');
        $("#page_"+main_index).addClass('selectedPage');
        $("#brand_list").attr("disabled","disabled")
        
        var tablename=$("#page_"+main_index).attr('class');
        var classarray= tablename.split(" ");
        set_question_link(classarray[1])
        console.log(classarray[1]);
    }
    function fetch_post_dropdown(qa_id){
        var channel=$('#channel_name').val();
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'qa_id':qa_id,
                'channel':channel,
                'action':'fetch_post_dropdown'
            },
            beforeSend: function(){
                //                unoload();
            },
            success:function(data){
                var obj=$.parseJSON(data);
                if(obj.dropdown_html!=''){
                    $("#inputed_answer").hide();
                    $("#dropdown_post").show();
                    $("#dropdown_post").html('');
                    $("#dropdown_post").html(obj.dropdown_html);
                    $("#inputed_answer").val($("#get_type_dropdown").val());
                }else{
                    $("#inputed_answer").show();
                    $("#dropdown_post").hide();
                }
                //                console.log(data);
                //                unoloaded();
            }});
    
    }
    function send_mail(){
        var contact_id=<?= $_SESSION['qa_contact_id'] ?>;
        var body=$(".query").html();
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'contact_id':contact_id,
                'msg_body':body,
                'action':'send_mail'
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                console.log(data);
                unoloaded();
            }});
    }
    
    function find_answeres(){
        //        var answere=$('#answere').val();
        var brand=$('#brand_list').val();
        if(brand=='' || brand==null){
            brand=$("#channel_brand_name").val();
        }
        var z_testid_pk=$('#z_testid_pk').val();
        var currentquestionId=$('#qa_id').val();
        var extra_id=$('#extra_id').val();
        //        var extra_id=$('#extra_id').val();
        var main_index=$('#main_index').val();
        //        var extra_id=$('#extra_id').val();
        var channel=$('#channel_name').val();
        var answere=$('#inputed_answer').val();
        var leavel='brands';
        if(ques[0]['questionType']=='users'){
            leavel='users';
            brand=$('#userhandle').val();
        }
        var pre_qa_id=main_qaid_arr[parseInt(main_index)-1];
        
        $.ajax({
            url:"findans.php",
            type:'post',
            data:{'z_testid_pk':z_testid_pk,'extra_id':extra_id,'qa_id':currentquestionId,'handler':brand,'find':answere,'channel':channel,'action':'findans','main_index':main_index,'pre_qa_id':pre_qa_id,'leavel':leavel},
            async:false,
            beforeSend: function(){
                unoload();
            },
            success:function( data )
            {
                console.log(data);
                var obj =$.parseJSON(data);
                $('#z_testid_pk').val(obj.z_testid_pk);
                if(obj.z_testid_pk>0){
                    $("#brand_search_box").attr("disabled","disabled");
                    $("#brand_list").attr("disabled","disabled");
                    //                    $(".dropdown-toggle").unbind("click");
                }
                $("#inputed_answer").show();
                $("#dropdown_post").hide();
                $("#inputed_answer").val('');
                $('#extra_id').val(obj.extra_id);
                console.log(obj.outputresult);
                $("#page_"+main_index).removeClass("pass_paginate");
                $("#page_"+main_index).removeClass("fail_paginate");
                if(obj.outputresult!=undefined){
                    if(obj.outputresult=='pass'){
                        console.log('if');
                        $("#page_"+main_index).addClass("pass_paginate");
                    }else if(obj.outputresult=='fail'){
                        console.log('else if');
                        $("#page_"+main_index).addClass("fail_paginate");
                    }else{
                        console.log('else ');
                        $("#page_"+main_index).addClass("fail_paginate");
                    }
                }
                if(parseInt(obj.samplesize)>1 && obj.extra_id==''){
                    //                                    console.log('increase question');
                    var pageid=$(".qa-paginate").find('.selectedPage').attr('id');
                    var result =pageid.split('_');
                    var id=parseInt(result[1])+1;
                    getPageData(id);
                                    
                }
                unoloaded();
                
            
            }
        });
    }
    function feel_input_answer(){
        $("#inputed_answer").val($("#get_type_dropdown").val());
    }
    function update_score(){
        var z_testid_pk=$('#z_testid_pk').val();
        var brand=$('#brand_list').val();
        if(brand=='' || brand==null){
            brand=$("#channel_brand_name").val();
        }
        var leavel='brands';
        if(ques[0]['questionType']=='users'){
            leavel='users';
        }
        var channel_name=$("#channel_name").val();
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'z_testid_fk':z_testid_pk,
                'brand_name':brand,
                'channel_name':channel_name,
                'action':'check_score',
                'leavel':leavel
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                console.log(data);
                unoloaded();
            }});
    }
    function getPageData(pageId){
    
        //        alert('hi');
        //        if(pageId<=ques_size){
        console.log('pageid-->'+pageId);
        $('#extra_id').val('');
        $('#inputed_answer').val('');
        var qa_index=main_que_arr[parseInt(pageId)-1];
        $("#qa_index").val(qa_index);
        $("#main_index").val(pageId);

        $("#trackNo").html((pageId)+"/"+ques_size+" :");
        $("#question").html(ques[qa_index]['question']);
        $('#qa_id').val(ques[qa_index]['qa_id']);

        //fill answer if already answered.
        //        if((typeof answers[ques[qa_index]['qa_id']]!=='undefined') && answers[ques[qa_index]['qa_id']]['ans']!='')
        //            $("#inputed_answer").val(answers[ques[qa_index]['qa_id']]['ans']);
        
        if(pageId>1){
            $("#prevPage").attr('onclick','getPageData('+(parseInt(pageId)-1)+')');
        }
        else{
            $("#prevPage").attr('onclick','getPageData(1)');
        }
        if(pageId<ques_size){
            $("#nextPage").attr('onclick','getPageData('+(parseInt(pageId)+1)+')');
        }
        else{
            $("#nextPage").attr('onclick','getPageData('+ques_size+')');
        }
        
        $(".pages").removeClass('selectedPage');
        $("#page_"+pageId).addClass('selectedPage');
        var tablename=$("#page_"+pageId).attr('class');
        var classarray= tablename.split(" ");
        set_question_link(classarray[1]);
        console.log(classarray[1]);
        //        var channel_name=$("#channel_name").val();
        //        var brand=$('#brand_list').val();
        //        var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
        //        if(brand=='' || brand==null){
        //            brand=$("#channel_brand_name").val();
        //        }
        //        if(classarray[1]=='users'){
        //            var userhandle=$("#userhandle").val();
        //            //            var channel_name=$("#channel_name").val();
        //            set_channel_iframe(userhandle,channel_name);
        //        }
        //        else if(classarray[1]=='posts'){
        //            //           if(ques[0]['questionType']=='posts'){
        //            var link=postID[0]['link'];
        //            console.log(postID);
        //            $("#extra_id").val('');
        //            $("#extra_id").val(postID[0]['postID']);
        //            $('#channel_iframe').html("");
        //            $('#post_link').val(''); 
        //            $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
        //            $('#post_link').val(link); 
        //            //                }
        //        }
        //        else if(classarray[1]=='userposts'){
        //            var link;
        //            //           if(ques[0]['questionType']=='posts'){
        //            //            https://www.facebook.com/40796308305/posts/10152148484323306
        //            if(channel_name=='facebook'){
        //                var postarr= userpostId[0]['postID'].split("_");
        //                link="https://www.facebook.com/"+ postarr[0]+"/posts/"+postarr[1];
        //                $("#extra_id").val('');
        //                $("#extra_id").val(userpostId[0]['postID']);
        //                $('#channel_iframe').html("");
        //                $('#post_link').val(''); 
        //                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
        //                $('#post_link').val(link); 
        //                //               postarr[0] postarr[1]
        //            }else if(channel_name=='twitter'){
        //                link=  "https://twitter.com/"+userpostId[0]['handle']+"/status/"+userpostId[0]['postID'];
        //                $("#extra_id").val('');
        //                $("#extra_id").val(userpostId[0]['postID']);
        //                $('#channel_iframe').html("");
        //                $('#post_link').val(''); 
        //                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
        //                $('#post_link').val(link); 
        //            }else if(channel_name=='youtube'){
        //                if(userpostId[0]['postID']==''){
        //                    set_channel_iframe(brand,"youtube");
        //                }else{
        //                    ("#extra_id").val('');
        //                    $("#extra_id").val(userpostId[0]['postID']);
        //                    $('#channel_iframe').html("");
        //                    $('#post_link').val(''); 
        //                    $('#channel_iframe').html("<a href='"+userpostId[0]['link']+"' target='_blank'>"+userpostId[0]['link']+"<a>"+lable+" ")
        //                    $('#post_link').val(userpostId[0]['link']); 
        //                }
        //            }
        //        }
        //        else if(classarray[1]=='engagements'){
        //            //           if(ques[0]['questionType']=='posts'){
        //            if(channel_name=='facebook' || channel_name=='instagram' || channel_name=='youtube'){
        //                var link=postID[0]['link'];
        //                console.log(postID);
        //                $("#extra_id").val('');
        //                $("#extra_id").val(postID[0]['postID']);
        //                $('#channel_iframe').html("");
        //                $('#post_link').val(''); 
        //                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
        //                $('#post_link').val(link); 
        //            }else{
        //                var link=  "https://twitter.com/"+engpostId[0]['handle']+"/status/"+engpostId[0]['engagementID'];
        //                console.log(engpostId)
        //                $("#extra_id").val('');
        //                $("#extra_id").val(engpostId[0]['engagementID']);
        //                $('#channel_iframe').html("");
        //                $('#post_link').val(''); 
        //                $('#channel_iframe').html("<a href='"+link+"' target='_blank'>"+link+"<a>"+lable+" ")
        //                $('#post_link').val(link); 
        //            }
        //           
        //            //                }
        //        }else{
        //            set_channel_iframe(brand,channel_name);
        //        }
    }
    function fetchresultpage(z_testid_fk){
        //        alert(testid)
        //        var leavel='brands';
        //        if(ques[0]['questionType']=='users'){
        //            leavel='users';
        //        }
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'z_testid_fk':z_testid_fk,
                'action':'fetchresultpage'
                //                'leavel':leavel
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                console.log(data);
                var obj =$.parseJSON(data);
                var question_array=obj.result_query;
                $("#history_review_data").css("display",'none');
                $("#display_result_page").css("display",'block');
                $("#main_frame").css("display",'none');
                $("#email_page").css("display",'none');
                $("#data").css("display",'none');
                $("#top_bar").css("display",'none');
                $("#data_qapage").css("display",'block');
                $("#history_data").removeClass("selected");
                $(".brand").css("text-decoration","underline").css("color","#085394");
                $("#edit_button").attr("onclick","editresultpage()")
                
                $("#z_testid_pk").val(z_testid_fk);
                
                var querymain='';
                var summary='<table width="100%" border="0" cellspacing="0" cellpadding="0" id="example"><tr><td style="text-align:center; font-weight: bold;">Summary</td></tr><table width="100%" border="0" cellspacing="0" cellpadding="0" class="summary-detail"><tr><td style="width: 12%;">Previously Checked:</td><td style="width: 15%;">'+obj.checked_date+'</td><td style="width: 4%;">Score:</td><td style="width: 4%;">'+ obj.score+'/'+obj.testsize+'</td><td style="width: 4%;">Brands:</td><td style="width: 8%;">'+obj.handle+'</td><td style="width: 5%;">Channel</td><td style="width: 7%;">'+question_array[0].channel_name+'</td><td style="width: 4%;">Status:</td><td style="width: 5%;">'+obj.status+'</td><td style="width: 8%;">Varified By:</td><td style="width: 13%;">'+obj.username+'</td></tr></table></table>';
                querymain+=summary;
                
                if(question_array.length>0){
                    //                    var querymain='';
                    $("#channel_name").val(question_array[0].channel_name);
                    $("#channel_brand_name").val(question_array[0].brand_name);
                
                    for(i=0;i<question_array.length;i++){
                        //                        if(i % 2 == 0){
                        //                            var trclass="qa-even";
                        //                        }else{
                        //                            var trclass="qa-odd";
                        //                        }
                        if(question_array[i].passed=='1'){
                            var status="Pass";
                            //                            var status_class= "green";
                        }else if(question_array[i].passed=='-1'){
                            var status="Fail";
                            //                            var status_class= "red";
                            
                        }else{
                            var status="Not Answred!";
                            //                            var status_class= "dark-yellow";
                        }
                        //                        var str=obj[0][i].channel;
                        //                        var brandid=str.toLowerCase();
                        //                        var z_testid_pk=obj[0][i].z_testid_pk
                        var query='<div class="number">'+(parseInt(i)+1)+'</div><div class="clear"></div><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="13%"><label>Question : </label></td><td width="87%"><span>'+question_array[i].question+'</span>  </td></tr><tr><td><label>answer : </label></td><td><span>'+question_array[i].input+'</span>  </td></tr><tr><td><label>expected answer : </label></td><td><span>'+question_array[i].expected+'</span>  </td></tr><tr><td><label>result : </label></td><td><span>'+status+'</span>  </td></tr></tbody></table><div class="clear"></div>';
                        //                        var query='<div class="number">'+(parseInt(i)+1)+'</div><div class="clear"></div><label>Question : </label><span id="result_question">'+question_array[i].question+'</span><br/><label>answer : </label><span id="result_answer">'+question_array[i].input+'</span><br/><label>expected answer : </label><span id="result_expected_answer">'+question_array[i].expected+'</span><br/><label>result : </label><span id="result_pass_fail">'+status+'</span><br/><div class="clear"></div>';
                        //                    if(brandid!='facebook'){
                        //                        var  tbodymid='<tr class="'+trclass+'"><td style="text-align: left;"><span id="'+brandid+'" style="font-size:14px;cursor:pointer;" onclick=fetchqapage(this.id);><img src="'+obj[0][i].img+'" alt="" />'+obj[0][i].channel+'</span></td><td>'+obj[0][i].checked_date+'</td><td>'+obj[0][i].username+'</td><td class="'+status_class+' large-font">'+obj[0][i].score+'/'+obj[0][i].testsize+'</td><td class="green"><span style="cursor:pointer;" onclick="fetchresultpage('+z_testid_pk+')"><img src="'+status+'" alt="" /></span></td></tr>';
                        querymain+=query;
                        //                    }
                    
                   
                    }
                    $('.query').html('');
                    $('.query').append(querymain);
                    $("#result_display_view").removeClass("centerdiv");
                }else{
                    $("#result_display_view").html('');
                    $("#result_display_view").html('For this test their is no answer given !! ');
                    $("#result_display_view").addClass("centerdiv");
                }
                unoloaded();
                
                //                $("#history_review_data").css("display",'none');
                //                $("#history_review_data").css("display",'none');
            }});
    }
    function editresultpage(){
    
        //        alert("this page is under process!!")
        
        var channel_name=$("#channel_name").val();
        //        var brand_name=$("#channel_brand_name").val();
        var z_testid_fk=$("#z_testid_pk").val();
        //        var leavel='brands';
        //        if(ques[0]['questionType']=='users'){
        //            leavel='users';
        //            brand_name=$('#userhandle').val();
        //        }
        $.ajax({
            url:'qa_ops.php',
            type: 'post',
            data: {
                'z_testid_fk':z_testid_fk,
                'channel_name':channel_name,
                //                'brand_name':brand_name,
                'action':'editresultpage'
                //                'leavel':leavel
            },
            beforeSend: function(){
                unoload();
            },
            success:function(data){
                unoloaded();
                $("#top_bar").css('display','none');
                console.log(data);
                var obj =$.parseJSON(data);
                $("#history_data").removeClass("selected");
                $("#review_data").removeClass("selected");
                
                $("#main_frame").css("display","block");
                $("#history_review_data").css("display","none");
                var lable='<div style="color: black;">please click on above Link & answer the question stays in below Link</div>';
                if(obj.channel=='instagram'){
                    $('#channel_iframe').html("<a href='http://www.instagram.com/"+obj.handle+"' target='_blank'>http://www.instagram.com/"+obj.handle+"<a>"+lable+" ")
                }else if(obj.channel=='facebook'){
                    $('#channel_iframe').html("<a href='http://www.facebook.com/"+obj.handle+"' target='_blank'>http://www.facebook.com/"+obj.handle+" <a>"+lable+" ")
                        
                }else if(obj.channel=='twitter'){
                    $('#channel_iframe').html("<a href='http://www.twitter.com/"+obj.handle+"' target='_blank'>http://www.twitter.com/"+obj.handle+" <a>"+lable+" ")
                             
                }else{
                    $('#channel_iframe').html("<a href='http://www.youtube.com/"+obj.handle+"' target='_blank'>http://www.youtube.com/"+obj.handle+" <a>"+lable+" ")
                }
                
                ques=(obj.question);
                answers=(obj.answers);
                ques_size=(obj.quesize);
                all_ques_size=(obj.allquesize);
                $("#qa_index").val(0);
                $("#main_index").val(1);
                $("#trackNo").html("1/"+obj.quesize+" :");
                if(answers[ques[0]['qa_id']]['ans']!=''){
                    $("#inputed_answer").val(answers[ques[0]['qa_id']]['ans']);
                }
                if(obj.leavel=='users'){
                    $('#userhandle').val(obj.handle);
                }else{
                    $("#brand_list").val(obj.handle);
                }
               
                //            if(answers[ques[0]['qa_id']]['passed']==1)
                //                $("#page_1").addClass("pass_paginate");
                //            else
                //                $("#page_1").addClass("fail_paginate");
                $("#question").html(ques[0]['question']);
                $("#qa_id").val(ques[0]['qa_id']);
                
                $(".qa-paginate").html(obj.pagination);
                $("#prevPage").attr('onclick','getPageData(1)');
                $("#nextPage").attr('onclick','getPageData(2)');

                $(".pages").removeClass('selectedPage');
                $("#page_1").addClass('selectedPage');
                main_que_arr=[];
                for(var i=0;i<ques.length;i++){
                    //                    console.log(i + " -- "+ques[i]['question']);
                    if(ques[i]['parent_qa_id']==0){ //means it is main que
                        main_que_arr.push(i);
                    }
                }
                //                console.log(main_que_arr);
                $("#display_result_page").css("display",'none');
                $("#top_bar").css("display",'none');
            
            }
        });
    }
</script>
<script type="text/javascript">
    //    (function( $ ) {
    //https://twitter.com/kiranjoshi36/status/363531925379489793
</script>
<div class="tab_container">
    <div id="tab3" class="tab_content" style="padding: 1px;">
        <div class="tab-nav">
            <ul class="tab-nav">
                <li><a class="facebook brand" href="javascript:fetchqapage('facebook');">Facebook</a></li>
                <li><a class="twitter brand" href="javascript:fetchqapage('twitter');">Twitter</a></li>
                <li><a class="youtube brand" href="javascript:fetchqapage('youtube');">Youtube</a></li>
                <li><a class="instagram brand" href="javascript:fetchqapage('instagram');" style="border:none;" >Instagram</a></li>
                <!--                <li><a style="border:none;" data-href="pinterest2">Pinterest</a></li>-->
            </ul>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="qa-content">
            <div id="data">
            </div>
            <div id="data_qapage" style="display: none;">
                <?php include_once 'qa_brandpage.php'; ?>
            </div>
            <div id="email_page">
                <div style="margin:10px 0 0 15px;">Send Email Updates To:
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <div class="submit-email">
                    <div style="float:left; width:86%; margin-left: 15px;">
                        <input type="text" name="mailTo" id="mailTo">
                    </div>
                    <div class="btn-save"><a href="javascript:void(0);" onclick="javascript:addSendMailIds(<?= $_SESSION['qa_contact_id'] ?>);">save</a></div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <?php include_once 'footer.php'; ?>
<!--    <script src="js/bootstrap.js" type="text/javascript"></script>-->
<!--    <script src="js/bootstrap-combobox.js" type="text/javascript"></script>-->
</div>
</div>

</body>
</html>
