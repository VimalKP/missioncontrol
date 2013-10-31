/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function logout(){
    $.ajax({
        url:'login_ops.php',
        type: 'post',
        data: {
            'action':'logout'
        },
        success:function(data){
            window.location.href="index.html";
            return false;
        }
    });
}

//function fillEmailTo(contactId){
//    $.ajax({
//        url:'qa_ops.php',
//        type: 'post',
//        data: {
//            'contactId':contactId,
//            'action':'getEmailTo'
//        },
//        success:function(emails){
//            $("#mailTo").val(emails)
//        }
//    });
//}

function addSendMailIds(contactId){
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var email=($("#mailTo").val());
    email=email.replace(/\s/g, '')
    if(email==''){
        alert("Missing : Email(s)");
        return false;
    }
    else{
        var emails = email.split(/[;,]+/); // split element by , and ;
        var valid = true;
        for (var i in emails) {
            var value = emails[i];
            valid = valid && regex.test(value);
        }
        if(valid){
            $.ajax({
                url:'qa_ops.php',
                type: 'post',
                data: {
                    'email':email,
                    'contactId':contactId,
                    'action':'addEmailTo'
                },
                success:function(res){
                    alert("Email(s) added successfully.");
                }
            });
        }
        else{
            alert('Error : Email Not Valid');
            return false;
        }
    }
}
function unoload(text , zindex)
{
    var html = jQuery('html');
    html.css('overflow', 'hidden').scrollTop();
    var horizontalCenter = ($(window).width()/2)-85;
    var verticalCener = (html.outerHeight()/2)-85;
    var height=$( document ).height();
    zindex =zindex || 700;
    text=typeof(text)!='undefined'?text:'Loading';
    var textobj = $('<span/>').html('&nbsp;&nbsp;'+text+'&nbsp;&nbsp;');
    var randomoverlay=$('<div class="unoapploadingoverlay"  style="position:absolute;height:'+height+'px;width:100%;background:#f5f5f5;z-index:'+zindex+';opacity:0.7;filter: alpha(opacity=70);-moz-opacity:0.7;-khtml-opacity: 0.7;top:0"></div>');
    text = '&nbsp;&nbsp;'+text+'&nbsp;&nbsp;';
    var str=['.&nbsp;&nbsp;','..&nbsp;','...']; 
    var dots = $('<span></span>') ;
    var text_span = $('<span></span>').append(textobj , dots); 
    var loadingdiv=$('<div class="transparentpopup" id="unoapploading" style="padding-top:'+verticalCener+'px;line-height: 40px;position: absolute;padding-left:'+horizontalCenter+'px"></div>').css({
        "z-index": zindex+1
    });

    var loadimg=$('<img id="rimg" src="images/loading.gif" style="vertical-align:middle;" width="35"  />');
    $('body').prepend(randomoverlay);
    $('body').prepend(loadingdiv);
    loadingdiv.prepend(loadimg,text_span);
    return textobj;
}

 
function unoloaded()
{
    var html = jQuery('html');
    $("#unoapploading").animate({
        opacity:'0.1'
    },function(){
        $('#unoapploading, .unoapploadingoverlay').remove();
        html.css('overflow', 'scroll').scrollTop();
    });
    
    
//$("#unoapploading").css("display","");
        
}
function get_manage_page(page,search){
    search = typeof search !== 'undefined' ? search : "";
    oTable.fnClearTable();
    $("#selectedPage").val(page);
    $('.addBtn').html(page);
    $('.menu').removeClass('selected');
    $('#'+page).addClass('selected');
    
    $.ajax({
        url:'managepage_ops.php',
        type: 'post',
        data: {
            'managepage':page,
            'action':'getPage',
            'page':$("#page").val(),
            'search':search
        },
        async:false,
        beforeSend: function(){
            unoload();
        },
        success:function(data){
            $("#sorting_paginate,#sorting_info,#sorting_length,#sorting_filter").hide();
            var obj =$.parseJSON(data);
            $("#pageBody").html("");
            //                var htmls='';
            $.each(obj,function(k,v){
                $("#pagination_ul").html(v.pagination);
            });
            //                $("#pageBody").html(htmls);
            $.each($.parseJSON(data), function(id,val) {
                var fanGrowth=parseInt(this.end_fans)-parseInt(this.start_fans);
                var perFan=(fanGrowth/this.start_fans);
                if(id!=''){
                    if(page=='instagram'){
                        var mainid="<a href='http://www.instagram.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;' target='_blank'>"+id+"<a>";
                    }else if(page=='facebook'){
                        var mainid="<a href='http://www.facebook.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
                    //                            $('#channel_iframe').html("<a href='http://www.facebook.com/"+obj.handle+"' target='_blank'>http://www.facebook.com/"+obj.handle+" <a>"+lable+" ")
                        
                    }else if(page=='twitter'){
                        var mainid="<a href='http://www.twitter.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
                    //                            $('#channel_iframe').html("<a href='http://www.twitter.com/"+obj.handle+"' target='_blank'>http://www.twitter.com/"+obj.handle+" <a>"+lable+" ")
                             
                    }else{
                        var mainid="<a href='http://www.youtube.com/"+id+"' style='text-decoration: none;cursor: pointer;color:#333333;'  target='_blank'>"+id+"<a>";
                    //                            $('#channel_iframe').html("<a href='http://www.youtube.com/"+obj.handle+"' target='_blank'>http://www.youtube.com/"+obj.handle+" <a>"+lable+" ")
                    }
                    var a = oTable.fnAddData([
                        mainid,
                        (typeof this.first_date=="undefined")?'-':this.first_date,
                        (typeof this.last_date=="undefined")?'-':this.last_date,
                        (typeof this.start_fans=="undefined")?'-':this.start_fans,
                        (typeof this.end_fans=="undefined")?'-':this.end_fans,
                        (isNaN(fanGrowth))?'0':fanGrowth,
                        (isNaN(perFan))?'0':perFan.toFixed(3),
                        (typeof this.engagements=="undefined")?'-':this.engagements,
                        (typeof this.users=="undefined")?'-':this.users,
                        '<a data-toggle="modal" href="#delModal" onclick="javascript:$(\'#delId\').val(\''+id+'\');"><img id='+id+' src="images/icon-delete.png"></a>']
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
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}